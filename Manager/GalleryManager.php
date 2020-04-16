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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Gallery;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class GalleryManager
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
     * @var int
     */
    public $lastCount;

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
     * @param array  $query
     *
     * @return null|Gallery
     */
    public function get(string $slug, array $query = []): ?Gallery
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/galleries/'.$slug, [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Gallery $data */
            $data = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Gallery::class, JsonEncoder::FORMAT);
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $data;
    }
}
