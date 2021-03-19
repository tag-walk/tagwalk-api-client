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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CityManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'name:asc';

    public int $lastCount = 0;
    private ApiProvider $apiProvider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @return City[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 100,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS
    ): array {
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/cities', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => $query,
        ]);

        $results = [];
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return $results;
        }

        $data = json_decode((string) $apiResponse->getBody(), true);
        foreach ($data as $datum) {
            $results[] = $this->serializer->denormalize($datum, City::class);
        }

        $this->lastCount = $apiResponse->getHeaderLine('X-Total-Count');

        return $results;
    }

    /**
     * @return City[]
     */
    public function listFilters(
        ?string $type = null,
        ?string $season = null,
        ?string $designer = null,
        ?string $tags = null,
        ?string $models = null,
        ?string $language = null
    ): array {
        $query = array_filter(compact('type', 'season', 'designer', 'tags', 'models', 'language'));
        $results = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/cities/filter', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, City::class);
            }
        }

        return $results;
    }

    /**
     * @return City[]
     */
    public function listFiltersStreet(
        ?string $season,
        ?string $designers,
        ?string $individuals,
        ?string $tags,
        ?string $language = null
    ): array {
        $results = [];
        $query = array_filter(compact('season', 'designers', 'individuals', 'tags', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/cities/filter-streetstyle', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, City::class);
            }
        }

        return $results;
    }

    public function create(City $city): ?City
    {
        $apiResponse = $this->apiProvider->request('POST', '/api/cities', [
            RequestOptions::JSON => $this->serializer->normalize($city, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_CREATED) {
            return null;
        }

        /** @var City $city */
        $city = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), City::class);

        return $city;
    }

    public function get(string $slug): ?City
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/cities/' . $slug, [
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var City $city */
        $city = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), City::class);

        return $city;
    }

    public function update(City $city): ?City
    {
        $apiResponse = $this->apiProvider->request('PUT', '/api/cities/' . $city->getSlug(), [
            RequestOptions::JSON => $this->serializer->normalize($city, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var City $city */
        $city = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), City::class);

        return $city;
    }

    public function delete(City $city): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', '/api/cities/' . $city->getSlug(), [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => ['refresh' => true]
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function toggleStatus(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PATCH, sprintf('/api/cities/%s/status', $slug), [
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function autocomplete(string $search)
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/cities/autocomplete', [
            RequestOptions::QUERY => compact('search'),
            RequestOptions::HTTP_ERRORS => true
        ]);

        return json_decode($apiResponse->getBody(), true);
    }
}
