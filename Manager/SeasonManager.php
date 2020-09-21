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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Season;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class SeasonManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'position:asc';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

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
     * @param string|null $language
     * @param int|null    $from
     * @param int|null    $size
     * @param string|null $sort
     * @param string|null $status
     * @param bool|null   $shopable
     *
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
        $results = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language', 'shopable'));
        $apiResponse = $this->apiProvider->request('GET', '/api/seasons', [
            'query'       => $query,
            'http_errors' => false,
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
     * @param null|string $type
     * @param null|string $city
     * @param null|string $designer
     * @param null|string $tags
     * @param null|string $models
     * @param string|null $language
     *
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
     * @param string|null $city
     * @param string|null $designers
     * @param string|null $individuals
     * @param string|null $tags
     * @param string|null $language
     *
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
}
