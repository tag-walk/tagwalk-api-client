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

use DateInterval;
use DateTime;
use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Tagwalk\ApiClientBundle\Security\ApiAuthenticator;

class ApiProvider
{
    /** @var string session key for access token bearer */
    private const ACCESS_TOKEN = 'access_token';
    /** @var string session key for refresh token bearer */
    private const REFRESH_TOKEN = 'refresh_token';
    /** @var string session key for token expiration date */
    private const TOKEN_EXPIRATION = 'token_expiration';
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
     * @param RequestStack     $requestStack
     * @param SessionInterface $session
     * @param string           $baseUri
     * @param string           $clientId
     * @param string           $clientSecret
     * @param string           $redirectUri
     * @param string           $environment
     * @param float            $timeout
     * @param bool             $lightData do not resolve files path property
     * @param bool             $analytics
     * @param string|null      $cacheDirectory
     * @param string|null      $showroom
     */
    public function __construct(
        RequestStack $requestStack,
        SessionInterface $session,
        string $baseUri,
        string $clientId,
        string $clientSecret,
        string $redirectUri = null,
        string $environment = 'prod',
        float $timeout = self::DEFAULT_TIMEOUT,
        bool $lightData = true,
        bool $analytics = false,
        ?string $cacheDirectory = null,
        ?string $showroom = null
    ) {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->lightData = $lightData;
        $this->analytics = $analytics;
        $this->showroom = $showroom;
        $this->client = $this->createClient($baseUri, $environment, $timeout, $cacheDirectory);
    }

    /**
     * @param string $baseUri
     * @param int    $timeout
     * @param string $cacheDirectory
     * @param string $environment
     *
     * @return Client
     */
    private function createClient(
        string $baseUri,
        string $environment,
        int $timeout = self::DEFAULT_TIMEOUT,
        ?string $cacheDirectory = null
    ): Client {
        $params = [
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ];
        if ($environment === 'prod') {
            $params['handler'] = $this->getClientCacheHandler($cacheDirectory);
        }

        return new Client($params);
    }

    /**
     * @param string|null $cacheDirectory
     *
     * @return HandlerStack
     */
    private function getClientCacheHandler(?string $cacheDirectory): HandlerStack
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
        $response = $this->client->request($method, $uri, $options);
        if ($response->getStatusCode() === Response::HTTP_UNAUTHORIZED) {
            $this->session->remove(self::ACCESS_TOKEN);
        }

        return $response;
    }

    /**
     * @return array
     */
    private function getDefaultOptions(): array
    {
        return [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::HEADERS     => array_filter([
                'Authorization'         => $this->getBearer(),
                'Accept'                => 'application/json',
                'Accept-Language'       => $this->requestStack->getCurrentRequest()
                    ? $this->requestStack->getCurrentRequest()->getLocale()
                    : 'en',
                'X-AUTH-TOKEN'          => $this->session->get(ApiAuthenticator::USER_TOKEN),
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
        if (false === $this->session->has(self::ACCESS_TOKEN)) {
            $this->authenticate();
        } else {
            $now = new DateTime();
            $refreshToken = $this->session->get(self::REFRESH_TOKEN);
            $tokenExpiration = $this->session->get(self::TOKEN_EXPIRATION);
            if ($refreshToken && $tokenExpiration && $now->modify('+ 5 seconds') > $tokenExpiration) {
                $this->refreshToken($refreshToken);
            }
        }

        return "Bearer {$this->session->get(self::ACCESS_TOKEN)}";
    }

    /**
     * @param ResponseInterface $response
     *
     * @throws BadRequestHttpException thrown if state value sent by the api is not the same as the state sent in previous request
     */
    private function tokenResponseToSession(ResponseInterface $response): void
    {
        $auth = json_decode($response->getBody(), true);
        if (isset($auth['state']) && $auth['state'] !== $this->session->get(self::AUTHORIZATION_STATE)) {
            throw new BadRequestHttpException('Incorrect state value.');
        }
        $this->session->set(self::ACCESS_TOKEN, $auth['access_token']);
        $this->session->set(self::TOKEN_EXPIRATION, (new DateTime())->add(new DateInterval(sprintf('PT%dS', $auth['expires_in']))));
        if (isset($auth['refresh_token'])) {
            $this->session->set(self::REFRESH_TOKEN, $auth['refresh_token']);
        } else {
            $this->session->remove(self::REFRESH_TOKEN);
        }
    }

    private function authenticate(): void
    {
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
        $this->tokenResponseToSession($response);
    }

    /**
     * @param string $code
     */
    public function authorize(string $code): void
    {
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
                    'X-AUTH-TOKEN'          => $this->session->get(ApiAuthenticator::USER_TOKEN),
                    'Tagwalk-Showroom-Name' => $this->showroom,
                ]),
                RequestOptions::HTTP_ERRORS => true,
            ]
        );

        $this->tokenResponseToSession($response);
    }

    /**
     * @return array
     */
    public function getAuthorizationQueryParameters(): array
    {
        $state = hash('sha512', random_bytes(32));
        $this->session->set(self::AUTHORIZATION_STATE, $state);

        return array_filter([
            'response_type'         => 'code',
            'state'                 => $state,
            'client_id'             => $this->clientId,
            'redirect_uri'          => $this->redirectUri,
            'x-auth-token'          => $this->session->get(ApiAuthenticator::USER_TOKEN),
            'tagwalk-showroom-name' => $this->showroom,
        ]);
    }

    /**
     * @param string $token
     */
    private function refreshToken(string $token): void
    {
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
        $this->tokenResponseToSession($response);
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
