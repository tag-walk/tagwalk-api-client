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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Agency;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class AgencyManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer;
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider          $apiProvider
     * @param SerializerInterface  $serializer
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $slug
     *
     * @return Agency|null
     */
    public function get(string $slug): ?Agency
    {
        $record = null;
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            "/api/agencies/{$slug}",
            [RequestOptions::HTTP_ERRORS => false]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Agency $record */
            $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Agency::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('AgencyManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $record;
    }
}
