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
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CityManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'name:asc';

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
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     *
     * @return City[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 100,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS
    ): array {
        $results = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/cities', [
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
     * @param null|string $type
     * @param null|string $season
     * @param null|string $designer
     * @param null|string $tags
     * @param null|string $models
     * @param string|null $language
     *
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
     * @param string|null $season
     * @param string|null $designers
     * @param string|null $individuals
     * @param string|null $tags
     * @param string|null $language
     *
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
}
