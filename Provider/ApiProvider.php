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
use GuzzleHttp\Exception\RequestException;

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
     * @param string $baseUri
     * @param string $clientId
     * @param string $clientSecret
     */
    public function __construct(string $baseUri, string $clientId, string $clientSecret)
    {
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->client = new Client([
            'base_uri' => $baseUri,
            'timeout' => 10.0
        ]);
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array $query
     *
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function request($method, $uri, $query): bool
    {
        $options = [
            'http_errors' => true,
            'headers' => [
                'Authorization' => $this->getBearer(),
                'Accept' => 'application/json'
            ]
        ];
        if ($query) {
            $options['query'] = $query;
        }
        try {
            $response = $this->client->request($method, $uri, $options);

            return $response;
        } catch (RequestException $e) {
            return false;
        }
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
                'form_params' => [
                    'client_id' => $this->clientId,
                    'client_secret' => $this->clientSecret,
                    'grant_type' => 'client_credentials'
                ]
            ]
        );
        $data = json_decode($response->getBody(), true);

        $this->token = $data['access_token'];
    }
}