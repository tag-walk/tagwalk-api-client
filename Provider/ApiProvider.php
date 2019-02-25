<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\RequestOptions;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Session\SessionInterface;

class ApiProvider
{
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
     * @param RequestStack $requestStack
     * @param SessionInterface $session
     * @param string $baseUri
     * @param string $clientId
     * @param string $clientSecret
     * @param float $timeout
     */
    public function __construct(
        RequestStack $requestStack,
        SessionInterface $session,
        string $baseUri,
        string $clientId,
        string $clientSecret,
        $timeout = 10.0
    ) {
        $this->requestStack = $requestStack;
        $this->session = $session;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => $timeout
        ]);
        $this->cache = new FilesystemAdapter('tagwalk_api_client');
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     * @return mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, $options = [])
    {
        $default = [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::HEADERS => [
                'Authorization' => $this->getBearer(),
                'Accept' => 'application/json',
                'Accept-Language' => $this->requestStack->getCurrentRequest()
                    ? $this->requestStack->getCurrentRequest()->getLocale()
                    : 'en', // Fallback if console mode
                'Cookie' => $this->session->get('Cookie')
            ]
        ];
        $options = array_merge($default, $options);
        $response = $this->client->request($method, $uri, $options);

        return $response;
    }

    /**
     * @return string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getBearer()
    {
        $token = $this->getToken();

        return "Bearer {$token}";
    }

    /** @noinspection PhpDocMissingThrowsInspection */
    /**
     * @return mixed|string
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function getToken()
    {
        if (null === $this->token) {
            /** @noinspection PhpUnhandledExceptionInspection */
            $tokenCache = $this->cache->getItem('token');
            if (!$tokenCache->isHit()) {
                $auth = $this->authenticate();
                $tokenCache->set($auth['access_token']);
                $tokenCache->expiresAfter(intval($auth['expires_in']) - 5);
            }
            $this->token = $tokenCache->get();
        }

        return $this->token;
    }

    /**
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    private function authenticate()
    {
        $response = $this->client->request(
            'POST',
            '/oauth/v2/token',
            [
                RequestOptions::FORM_PARAMS => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials'
                ]
            ]
        );

        return json_decode($response->getBody(), true);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}