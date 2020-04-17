<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Collection;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CollectionManager
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
     * @param string $type
     * @param string $designer
     * @param string $season
     * @param array  $query
     *
     * @return null|Collection
     */
    public function find(string $type, string $designer, string $season, array $query = []): ?Collection
    {
        $data = null;
        $apiResponse = $this->apiProvider->request(
            'GET',
            sprintf('/api/collections/%s/%s/%s', $type, $designer, $season),
            [
                RequestOptions::QUERY       => $query,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            /** @var Collection $data */
            $data = $this->serializer->denormalize($data, Collection::class);
        }

        return $data;
    }
}
