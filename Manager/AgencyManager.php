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
            $record = $this->serializer->deserialize((string) $apiResponse->getBody(), Agency::class, JsonEncoder::FORMAT);
        }

        return $record;
    }
}
