<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer  $serializer
     */
    public function __construct(ApiProvider $apiProvider, Serializer $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->logger = new NullLogger();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $id
     *
     * @return null|Config
     */
    public function get(string $id): ?Config
    {
        $config = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/config/'.$id, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $config = $this->serializer->denormalize($data, Config::class);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('ConfigManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $config;
    }

    /**
     * @param string $namespace
     *
     * @return array
     */
    public function list(string $namespace): array
    {
        $list = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/config', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => ['namespace' => $namespace],
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $datum) {
                    $list[] = $this->serializer->denormalize($datum, Config::class);
                }
            }
        } else {
            $this->logger->error('ConfigManager::list unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $list;
    }

    /**
     * @param string $key
     * @param string $value
     *
     * @return bool
     */
    public function set(string $key, string $value): bool
    {
        $apiResponse = $this->apiProvider->request('PUT', '/api/config/'.$key.'/'.$value);
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            $this->logger->error('ConfigManager::set unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);

            return false;
        }

        return true;
    }
}
