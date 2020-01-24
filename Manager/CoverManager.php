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

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Cover;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CoverManager
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
     * @return Cover
     */
    public function get(string $slug): ?Cover
    {
        $cover = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/covers/'.$slug, [
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if (Response::HTTP_OK === $apiResponse->getStatusCode()) {
            /** @var Cover $cover */
            $cover = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Cover::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('CoverManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $cover;
    }

    /**
     * @param Cover $cover
     *
     * @return Cover|null
     */
    public function update(Cover $cover): ?Cover
    {
        $updated = null;
        $apiResponse = $this->apiProvider->request('PUT', '/api/covers/'.$cover->getSlug(), [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $this->serializer->normalize($cover, null, ['write' => true]),
        ]);
        if (Response::HTTP_OK === $apiResponse->getStatusCode()) {
            /** @var Cover $updated */
            $updated = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Cover::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('CoverManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $updated;
    }
}
