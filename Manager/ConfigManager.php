<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;


use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\Config;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ConfigManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer $serializer
     */
    public function __construct(ApiProvider $apiProvider, Serializer $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param string $key
     * @return Config
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function get(string $key): Config
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/configs/' . $key);
        $data = json_decode($apiResponse->getBody(), true);
        $config = $this->serializer->denormalize($data, Config::class);

        return $config;
    }

    /**
     * @param string $namespace
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(string $namespace): array
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/configs', ['query' => ['namespace' => $namespace], 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data === null ? [] : $data;
    }

    /**
     * @param string $key
     * @param string $value
     * @return Config
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function set(string $key, string $value): Config
    {
        $apiResponse = $this->apiProvider->request('PUT', '/api/configs/' . $key . '/' . $value);
        $data = json_decode($apiResponse->getBody(), true);
        $config = $this->serializer->denormalize($data, Config::class);

        return $config;
    }
}
