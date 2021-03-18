<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Type;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class TypeManager
{
    public const DEFAULT_SORT = 'name:asc';

    public $lastCount = 0;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    )
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function create(Type $type): ?Type
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST,
            '/api/types',
            [
                RequestOptions::JSON => $this->serializer->normalize($type, null, ['write' => true])
            ]
        );
        if ($apiResponse->getStatusCode() !== Response::HTTP_CREATED) {
            return null;
        }
        /** @var Type $type */
        $type = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), Type::class);

        return $type;
    }

    public function get(string $slug): ?Type
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            sprintf('/api/types/%s', $slug),
            [
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }
        /** @var Type $type */
        $type = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), Type::class);

        return $type;
    }

    public function update(Type $type): ?Type
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PUT,
            sprintf('/api/types/%s', $type->getSlug()),
            [
                RequestOptions::JSON => $this->serializer->normalize($type, null, ['write' => true])
            ]
        );
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }
        /** @var Type $type */
        $type = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), Type::class);

        return $type;
    }

    public function list(
        ?string $language = null,
        ?int $from = 0,
        ?int $size = 20,
        ?string $sort = self::DEFAULT_SORT,
        ?string $status = Status::ENABLED
    ): array {
        $records = [];
        $query = compact('from', 'size', 'language', 'sort', 'status');
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/types',
            [
                RequestOptions::QUERY       => $query,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $records[] = $this->serializer->denormalize($datum, Type::class);
            }
            $this->lastCount = $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $records;
    }

    public function toggleStatus(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PATCH,
            sprintf('/api/types/%s/status', $slug),
            [
                RequestOptions::HTTP_ERRORS => false,
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    /**
     * @return Type[]
     */
    public function listFilters(
        ?string $season = null,
        ?string $city = null,
        ?string $designer = null,
        ?string $tags = null,
        ?string $models = null,
        ?string $language = null
    ): array {
        $query = array_filter(compact('season', 'city', 'designer', 'tags', 'models', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/types/filter', [
            RequestOptions::QUERY => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $results = [];
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return $results;
        }

        $data = json_decode((string) $apiResponse->getBody(), true);
        foreach ($data as $datum) {
            $results[] = $this->serializer->denormalize($datum, Type::class);
        }

        return $results;
    }

    public function autocomplete(string $search)
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/types/autocomplete', [
            RequestOptions::QUERY => ['search' => $search],
            RequestOptions::HTTP_ERRORS => true
        ]);

        return json_decode($apiResponse->getBody(), true);
    }
}
