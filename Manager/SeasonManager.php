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
use Tagwalk\ApiClientBundle\Model\Season;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class SeasonManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'position:asc';

    public int $lastCount = 0;
    private ApiProvider $apiProvider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @return Season[]
     */
    public function list(
        ?string $language = null,
        ?int $from = 0,
        ?int $size = 100,
        ?string $sort = self::DEFAULT_SORT,
        ?string $status = self::DEFAULT_STATUS,
        ?bool $shopable = false
    ): array {
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language', 'shopable'));
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons', [
            'query' => $query,
            'http_errors' => false,
        ]);

        $results = [];
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return $results;
        }

        $data = json_decode((string) $apiResponse->getBody(), true);
        foreach ($data as $datum) {
            $results[] = $this->serializer->denormalize($datum, Season::class);
        }

        $this->lastCount = $apiResponse->getHeaderLine('X-Total-Count');

        return $results;
    }

    /**
     * @return Season[]
     */
    public function listFilters(
        ?string $type = null,
        ?string $city = null,
        ?string $designer = null,
        ?string $tags = null,
        ?string $models = null,
        ?string $language = null
    ): array {
        $results = [];
        $query = array_filter(compact('type', 'city', 'designer', 'tags', 'models', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons/filter', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, Season::class);
            }
        }

        return $results;
    }

    /**
     * @return Season[]
     */
    public function listFiltersStreet(
        ?string $city,
        ?string $designers,
        ?string $individuals,
        ?string $tags,
        ?string $language = null
    ): array {
        $results = [];
        $query = array_filter(compact('city', 'designers', 'individuals', 'tags', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons/filter-streetstyle', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, Season::class);
            }
        }

        return $results;
    }

    public function create(Season $season): ?Season
    {
        $apiResponse = $this->apiProvider->request('POST', '/api/seasons', [
            RequestOptions::JSON => $this->serializer->normalize($season, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_CREATED) {
            return null;
        }

        /** @var Season $season */
        $season = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), Season::class);

        return $season;
    }

    public function get(string $slug): ?Season
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons/' . $slug, [
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var Season $season */
        $season = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), Season::class);

        return $season;
    }

    public function update(Season $season): ?Season
    {
        $apiResponse = $this->apiProvider->request('PUT', '/api/seasons/' . $season->getSlug(), [
            RequestOptions::JSON => $this->serializer->normalize($season, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var Season $season */
        $season = $this->serializer->denormalize(json_decode($apiResponse->getBody(), true), Season::class);

        return $season;
    }

    public function delete(Season $season): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', '/api/seasons/' . $season->getSlug(), [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => ['refresh' => true]
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function toggleStatus(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PATCH, sprintf('/api/seasons/%s/status', $slug), [
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function reorder(array $sortedSlugs): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PATCH, '/api/seasons/reorder', [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::FORM_PARAMS => ['slugs' => $sortedSlugs]
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }
}
