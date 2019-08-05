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

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ApiProvider
{
    /**
     * @var string cache key for access token bearer
     */
    private const CACHE_KEY_TOKEN = 'access_token';

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

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
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @var string
     */
    private $token;

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
        float $timeout = 30.0,
        bool $lightData = true,
        bool $analytics = false,
        ?string $cacheDirectory = null,
        ?string $showroom = null
    ) {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout'  => $timeout,
        ]);
        $this->lightData = $lightData;
        $this->analytics = $analytics;
        $this->cache = new FilesystemAdapter('tagwalk_api_client', 3600, $cacheDirectory);
        $this->showroom = $showroom;
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
            $this->cache->deleteItem(self::CACHE_KEY_TOKEN);
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
                // Fallback if console mode
                'Cookie'                => $this->session->get('Cookie'),
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
        $token = $this->getToken();

        return "Bearer {$token}";
    }

    /**
     * @return string
     */
    private function getToken(): string
    {
        if (null === $this->token) {
            return $this->cache->get(
                self::CACHE_KEY_TOKEN,
                function (ItemInterface $item) {
                    $auth = $this->authenticate();
                    $item->expiresAfter((int) $auth['expires_in'] - 5);

                    return $this->token = $auth['access_token'];
                }
            );
        }

        return $this->token;
    }

    /**
     * @return array
     */
    private function authenticate(): array
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

        return json_decode($response->getBody(), true);
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
