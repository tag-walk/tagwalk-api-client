<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Season;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class SeasonManager
{
    const DEFAULT_STATUS = 'enabled';
    const DEFAULT_SORT = 'position:asc';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     * @param int $cacheTTL
     * @param string $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer, int $cacheTTL = 3600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('seasons', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param string|null $language
     * @param int|null $from
     * @param int|null $size
     * @param string|null $sort
     * @param string|null $status
     * @param bool|null $shopable
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
        $key = md5(serialize($query));

        return $this->cache->get($key, function () use ($query) {
            $results = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/seasons', ['query' => $query, 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $results[] = $this->serializer->denormalize($datum, Season::class);
                }
            }

            return $results;
        });
    }

    /**
     * @param null|string $type
     * @param null|string $city
     * @param null|string $designer
     * @param null|string $tags
     * @param null|string $models
     * @param string|null $language
     * @return Season[]
     */
    public function listFilters(
        ?string $type,
        ?string $city,
        ?string $designer,
        ?string $tags,
        ?string $models,
        ?string $language = null
    ): array {
        $query = array_filter(compact('type', 'city', 'designer', 'tags', 'models', 'language'));
        $cacheKey = md5(serialize($query));

        $seasons = $this->cache->get($cacheKey, function () use ($query) {
            $results = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/seasons/filter', [
                RequestOptions::QUERY => array_merge($query, ['analytics' => 0]),
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $results[] = $this->serializer->denormalize($datum, Season::class);
                }
            }

            return $results;
        });

        return $seasons;
    }

    /**
     * @param null|string $city
     * @param null|string $designers
     * @param null|string $tags
     * @param string|null $language
     * @return Season[]
     */
    public function listFiltersStreet(
        ?string $city,
        ?string $designers,
        ?string $tags,
        ?string $language = null
    ): array {
        $query = array_filter(compact('city', 'designers', 'tags', 'language'));
        $cacheKey = md5(serialize($query));

        return $this->cache->get($cacheKey, function () use ($query) {
            $results = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/seasons/filter-streetstyle', [
                RequestOptions::QUERY => array_merge($query, ['analytics' => 0]),
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $results[] = $this->serializer->denormalize($datum, Season::class);
                }
            }

            return $results;
        });
    }
}
