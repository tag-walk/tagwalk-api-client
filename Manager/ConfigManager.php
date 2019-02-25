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
     * @param string $id
     * @return Config
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function get(string $id): Config
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/config/' . $id, ['http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);
        $config = $this->serializer->denormalize($data, Config::class);

        return $config;
    }

    /**
     * @param string $namespace
     * @return array
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function list(string $namespace): array
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/config', ['query' => ['namespace' => $namespace], 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);
        $list = [];
        if (!empty($data)) {
            foreach ($data as $datum) {
                $list[] = $this->serializer->denormalize($datum, Config::class);
            }
        }

        return $list;
    }

    /**
     * @param string $key
     * @param string $value
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function set(string $key, string $value): bool
    {
        $apiResponse = $this->apiProvider->request('PUT', '/api/config/' . $key . '/' . $value);

        return $apiResponse->getStatusCode() === 200;
    }
}
