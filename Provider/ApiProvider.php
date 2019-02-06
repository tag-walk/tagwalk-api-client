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
use Symfony\Component\HttpFoundation\RequestStack;

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
     * @var string
     */
    private $token;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @param RequestStack $requestStack
     * @param string $baseUri
     * @param string $clientId
     * @param string $clientSecret
     * @param float $timeout
     */
    public function __construct(RequestStack $requestStack, string $baseUri, string $clientId, string $clientSecret, $timeout = 10.0)
    {
        $this->requestStack = $requestStack;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => $timeout
        ]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $options
     *
     * @return bool|mixed|\Psr\Http\Message\ResponseInterface
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, $options = [])
    {
        $default = [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::HEADERS => [
                'Authorization' => $this->getBearer(),
                'Accept' => 'application/json',
                'Accept-Language' => $this->requestStack->getCurrentRequest()->getLocale()
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
    private function getBearer(): string
    {
        if (null === $this->token) {
            $this->authenticate();
        }

        return "Bearer {$this->token}";
    }

    /**
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
        $data = json_decode($response->getBody(), true);

        $this->token = $data['access_token'];
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->client;
    }
}