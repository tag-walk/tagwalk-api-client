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
     * @param ApiProvider          $apiProvider
     * @param SerializerInterface  $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
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
        }

        return $updated;
    }
}
