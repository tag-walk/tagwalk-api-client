<?php
/**
 * PHP version 7
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
     * @param ApiProvider         $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $slug
     *
     * @return Cover
     */
    public function get(string $slug): ?Cover
    {
        $cover = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/covers/' . $slug, [
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if (Response::HTTP_OK === $apiResponse->getStatusCode()) {
            $cover = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Cover::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('CoverManager::get invalid status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $cover;
    }
}
