<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Provider;

use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Security\ApiTokenStorage;

class ApiProvider
{
    /** @var string session key for authorization state value */
    private const AUTHORIZATION_STATE = 'auth_state';
    /** @var float api request default timeout */
    private const DEFAULT_TIMEOUT = 30.0;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var string
     */
    private $redirectUri;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var bool
     */
    private $lightData;

    /**
     * @var bool
     */
    private $analytics;

    /**
     * @var string
     */
    private $showroom;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var ApiTokenStorage
     */
    private $apiTokenStorage;

    /**
     * @param RequestStack          $requestStack
     * @param SessionInterface      $session
     * @param TokenStorageInterface $tokenStorage
     * @param ApiTokenStorage       $apiTokenStorage
     * @param string                $baseUri
     * @param string                $clientId
     * @param string                $clientSecret
     * @param string                $redirectUri
     * @param float                 $timeout
     * @param bool                  $lightData do not resolve files path property
     * @param bool                  $analytics
     * @param bool                  $httpCache
     * @param string|null           $cacheDirectory
     * @param string|null           $showroom
     * @param LoggerInterface|null  $logger
     */
    public function __construct(
        RequestStack $requestStack,
        SessionInterface $session,
        TokenStorageInterface $tokenStorage,
        ApiTokenStorage $apiTokenStorage,
        string $baseUri,
        string $clientId,
        string $clientSecret,
        string $redirectUri = null,
        float $timeout = self::DEFAULT_TIMEOUT,
        bool $lightData = true,
        bool $analytics = false,
        bool $httpCache = true,
        ?string $cacheDirectory = null,
        ?string $showroom = null,
        LoggerInterface $logger = null
    ) {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->apiTokenStorage = $apiTokenStorage;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->lightData = $lightData;
        $this->analytics = $analytics;
        $this->showroom = $showroom;
        $this->client = $this->createClient($baseUri, $timeout, $httpCache, $cacheDirectory);
        $this->logger = $logger ?? new NullLogger();
        $this->tokenStorage = $tokenStorage;
    }

    /**
     * @param string $baseUri
     * @param int    $timeout
     * @param bool   $httpCache
     * @param string $cacheDirectory
     *
     * @return Client
     */
    private function createClient(
        string $baseUri,
        int $timeout = self::DEFAULT_TIMEOUT,
        bool $httpCache = true,
        ?string $cacheDirectory = null
    ): Client {
        $params = [
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ];
        if ($httpCache) {
            $params['handler'] = $this->getClientHandlerStack($cacheDirectory);
        }

        return new Client($params);
    }

    /**
     * @param string|null $cacheDirectory
     *
     * @return HandlerStack
     */
    private function getClientHandlerStack(?string $cacheDirectory): HandlerStack
    {
        // Create a HandlerStack
        $stack = HandlerStack::create();
        // Add cache middleware to the top of the stack with `push`
        $stack->push(
            new CacheMiddleware(
                new PrivateCacheStrategy(
                    new Psr6CacheStorage(
                        new FilesystemAdapter('api-client-http-cache', 600, $cacheDirectory)
                    )
                )
            ),
            'cache'
        );

        return $stack;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function request($method, $uri, $options = []): ResponseInterface
    {
        $options = array_replace_recursive($this->getDefaultOptions(), $options);
        $this->logger->debug('requesting api', compact('method', 'uri', 'options'));
        $response = $this->client->request($method, $uri, $options);
        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED && strpos($uri, 'login') === false) {
            $this->logger->error('Unauthorized connection to API. Clearing token storage.');
            $this->apiTokenStorage->clear();
        }

        return $response;
    }

    /**
     * @return array
     */
    private function getDefaultOptions(): array
    {
        $token = $this->apiTokenStorage->get();

        return [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS     => array_filter([
                'Authorization'         => $this->getBearer(),
                'Accept'                => 'application/json',
                'Accept-Language'       => $this->requestStack->getCurrentRequest()
                    ? $this->requestStack->getCurrentRequest()->getLocale()
                    : 'en',
                'X-AUTH-TOKEN'          => $token ? $token->getUserToken() : null,
                'Tagwalk-Showroom-Name' => $this->showroom,
            ]),
            RequestOptions::QUERY       => [
                'light'     => $this->lightData,
                'analytics' => $this->analytics,
            ],
        ];
    }

    /**
     * @return string
     */
    private function getBearer(): string
    {
        $token = $this->apiTokenStorage->get();
        if (null === $token) {
            $this->authenticate();
        } else {
            $now = new DateTime();
            $refreshToken = $token->getRefreshToken();
            $tokenExpiration = $token->getExpiration();
            if ($tokenExpiration && $now->modify('+ 30 seconds') >= $tokenExpiration) {
                if ($refreshToken) {
                    $this->refreshToken($refreshToken);
                } else {
                    $this->authenticate();
                }
            }
        }
        // reload token
        $token = $this->apiTokenStorage->get();

        return "Bearer {$token->getAccessToken()}";
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws BadRequestHttpException thrown if state value sent by the api is not the same as the state sent in previous request
     */
    private function cacheTokenResponse(ResponseInterface $response): void
    {
        $auth = json_decode($response->getBody(), true);
        if (isset($auth['state']) && $auth['state'] !== $this->session->get(self::AUTHORIZATION_STATE)) {
            throw new BadRequestHttpException('Incorrect state value.');
        }
        $this->apiTokenStorage->save($auth);
    }

    private function authenticate(): void
    {
        $this->logger->debug('Getting API token from client_credentials.');
        $response = $this->client->request(
            'POST',
            '/oauth/v2/token',
            [
                RequestOptions::FORM_PARAMS => [
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type'    => 'client_credentials',
                ],
                RequestOptions::HTTP_ERRORS => true,
            ]
        );
        $this->cacheTokenResponse($response);
    }

    /**
     * @param string $code
     */
    public function authorize(string $code): void
    {
        $this->logger->debug('Getting API token from authorization_code.');
        $token = $this->apiTokenStorage->get();
        if ($token !== null && $token->getUserToken()) {
            $apiToken = $token->getUserToken();
        } else {
            $token = $this->tokenStorage->getToken();
            $user = $token->getUser();
            if ($user !== null && is_object($user) && $user instanceof User) {
                $apiToken = $user->getApiToken();
            }
        }
        if (empty($apiToken)) {
            throw new InvalidArgumentException('Unable to ask api for authorization code without user api token');
        }

        try {
            $response = $this->client->request(
                'POST',
                '/oauth/v2/token',
                [
                    RequestOptions::FORM_PARAMS => [
                        'grant_type'    => 'authorization_code',
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'redirect_uri'  => $this->redirectUri,
                        'code'          => $code,
                    ],
                    RequestOptions::HEADERS     => array_filter([
                        'X-AUTH-TOKEN'          => $apiToken,
                        'Tagwalk-Showroom-Name' => $this->showroom,
                    ]),
                    RequestOptions::HTTP_ERRORS => true,
                ]
            );
        } catch (ClientException $exception) {
            $this->logger->error('Error authorizing token', [
                'user_token' => $apiToken,
                'response'   => $exception->getResponse() ? json_decode($exception->getResponse()->getBody(), true) : null,
            ]);

            throw $exception;
        }

        $this->cacheTokenResponse($response);
    }

    /**
     * @param string|null $userToken
     *
     * @return array
     */
    public function getAuthorizationQueryParameters(?string $userToken): array
    {
        $state = hash('sha512', random_bytes(32));
        $this->session->set(self::AUTHORIZATION_STATE, $state);

        return array_filter([
            'response_type'         => 'code',
            'state'                 => $state,
            'client_id'             => $this->clientId,
            'redirect_uri'          => $this->redirectUri,
            'x-auth-token'          => $userToken,
            'tagwalk-showroom-name' => $this->showroom,
        ]);
    }

    /**
     * @param string $token
     */
    private function refreshToken(string $token): void
    {
        $this->logger->debug('Refreshing API token.');
        $response = $this->client->request(
            'POST',
            '/oauth/v2/token',
            [
                RequestOptions::FORM_PARAMS => [
                    'grant_type'    => 'refresh_token',
                    'client_id'     => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'refresh_token' => $token,
                ],
                RequestOptions::HTTP_ERRORS => true,
            ]
        );
        $this->cacheTokenResponse($response);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return PromiseInterface
     */
    public function requestAsync($method, $uri, $options = []): PromiseInterface
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        return $this->client->requestAsync($method, $uri, $options);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}
