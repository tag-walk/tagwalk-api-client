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
use Tagwalk\ApiClientBundle\Model\Individual;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ModelManager extends IndividualManager
{
    /**
     * @var int last list count
     */
    public $lastCount;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer $serializer
     * @param AnalyticsManager $analytics
     * @param int $cacheTTL
     * @param string|null $cacheDirectory
     */
    public function __construct(
        ApiProvider $apiProvider,
        Serializer $serializer,
        AnalyticsManager $analytics,
        int $cacheTTL = 600,
        string $cacheDirectory = null
    ) {
        parent::__construct($apiProvider, $serializer, $analytics, $cacheTTL, $cacheDirectory);
        $this->cache = new FilesystemAdapter('models', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param string $type
     * @param string $season
     * @param string $city
     * @param int $length
     *
     * @return array
     */
    public function whoWalkedTheMost($type = null, $season = null, $city = null, $length = 10)
    {
        $query = array_filter(compact('type', 'season', 'city', 'length'));
        $cacheKey = 'whoWalkedTheMost.' . md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        if ($this->cache->hasItem($cacheKey)) {
            $this->analytics->page('models_who_walked_the_most', array_merge($query, ['count' => $this->lastCount]));
        }

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/models/who-walked-the-most', [
                RequestOptions::QUERY => $query,
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                if (!empty($data)) {
                    foreach ($data as &$datum) {
                        $datum = $this->serializer->denormalize($datum, Individual::class);
                    }
                }
                $this->lastCount = (int)$apiResponse->getHeaderLine('X-Total-Count');
                $countCacheItem = $this->cache->getItem($countCacheKey)->set($this->lastCount);
                $this->cache->save($countCacheItem);
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
                $this->lastCount = 0;
            }

            return $data;
        });
    }

    /**
     * @param int $size
     * @param int $page
     * @param array $query
     * @return array
     */
    public function listMediasModels(int $size, int $page, array $query = []): array
    {
        $query = array_merge($query, [
            'size' => $size,
            'page' => $page
        ]);
        $cacheKey = 'listMediasModels.' . md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();
        if ($this->cache->hasItem($cacheKey)) {
            $this->analytics->page('model_list', array_merge($query, ['count' => $this->lastCount]));
        }

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/models', [
                RequestOptions::QUERY => $query,
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                if (!empty($data)) {
                    foreach ($data as &$datum) {
                        $datum = $this->serializer->denormalize($datum, Individual::class);
                    }
                }
                $this->lastCount = (int)$apiResponse->getHeaderLine('X-Total-Count');
                $countCacheItem = $this->cache->getItem($countCacheKey)->set($this->lastCount);
                $this->cache->save($countCacheItem);
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
                $this->lastCount = 0;
            }

            return $data;
        });
    }

    /**
     * @return Individual[]
     */
    public function getNewFaces(): array
    {
        $cacheKey = 'new-faces';
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        if ($this->cache->hasItem($cacheKey)) {
            $this->analytics->page('models_new_faces', ['count' => $this->lastCount]);
        }

        return $this->cache->get($cacheKey, function () use ($countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/models/new-faces', [RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                if (!empty($data)) {
                    foreach ($data as &$datum) {
                        $datum = $this->serializer->denormalize($datum, Individual::class);
                    }
                }

                $this->lastCount = (int)$apiResponse->getHeaderLine('X-Total-Count');
                $countCacheItem = $this->cache->getItem($countCacheKey)->set($this->lastCount);
                $this->cache->save($countCacheItem);
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
                $this->lastCount = 0;
            }

            return $data;

        });
    }

    /**
     * @param null|string $type
     * @param null|string $season
     * @param null|string $city
     * @param null|string $tags
     * @param string|null $language
     * @return Individual[]
     */
    public function listFilters(
        ?string $type,
        ?string $season,
        ?string $city,
        ?string $tags,
        ?string $language = null
    ): array {
        $models = [];
        $query = array_filter(compact('type', 'season', 'city', 'tags', 'language'));
        $cacheKey = 'listFilters' . md5(serialize($query));
        $cacheItem = $this->cache->getItem($cacheKey);
        if ($cacheItem->isHit()) {
            $models = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/models/filter', [
                RequestOptions::QUERY => array_merge($query, ['analytics' => 0]),
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $models[] = $this->serializer->denormalize($datum, Individual::class);
                }
                $cacheItem->set($models);
                $cacheItem->expiresAfter(3600);
                $this->cache->save($cacheItem);
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
                $this->lastCount = 0;
            }
        }

        return $models;
    }
}