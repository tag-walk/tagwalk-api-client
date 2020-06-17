<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Normalizer\AbstractObjectNormalizer;
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
     * @var int
     */
    public $lastCount;

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
            Request::METHOD_GET,
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

    /**
     * @param array $query
     *
     * $query['from']     = (int) (default: 0) offset from the first result
     * $query['size']     = (int) (default: 50) number of hits
     * $query['status']   = (string) filter on status
     *  {"enabled", "disabled"}
     * $query['type']     = (string) filter on type
     *  {"woman","man","accessory","accessory-man","couture","jewellery"}
     * $query['city']     = (string) filter on city
     * $query['season']   = (string) filter on season
     * $query['designer'] = (string) filter on designer
     * $query['language'] = (string) locale language
     *
     * @return null|Collection[]
     */
    public function list(array $query = []): ?array
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/collections',
            [
                RequestOptions::QUERY       => $query,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        $data = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            foreach ($data as &$datum) {
                $datum = $this->serializer->denormalize($datum, Collection::class);
            }
        }

        return $data;
    }

    /**
     * @param string $slug
     *
     * @return null|Collection
     */
    public function get(string $slug): ?Collection
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/collections/'.$slug
        );
        $data = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Collection $data */
            $data = $this->serializer->deserialize(
                (string) $apiResponse->getBody(),
                Collection::class,
                JsonEncoder::FORMAT
            );
        }

        return $data;
    }

    /**
     * @param string     $slug
     * @param Collection $record
     *
     * @return Collection
     */
    public function update(string $slug, Collection $record): ?Collection
    {
        $params = [
            RequestOptions::JSON => $this->serializer->normalize($record, null, [
                AbstractObjectNormalizer::SKIP_NULL_VALUES => true,
                'write'                                    => true,
            ]),
        ];
        $apiResponse = $this->apiProvider->request('PATCH', '/api/collections/'.$slug, $params);
        $updated = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            /** @var Collection $updated */
            $updated = $this->serializer->denormalize($data, Collection::class);
        }

        return $updated;
    }
}
