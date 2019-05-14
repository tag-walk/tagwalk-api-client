<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\MediaNormalizer;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class MediaManager
{
    /** @var int default list size */
    public const DEFAULT_SIZE = 12;

    /** @var string default list medias sort for a model */
    public const DEFAULT_MEDIAS_MODEL_SORT = 'created_at:desc';

    /**
     * @var int last query result count
     */
    public $lastCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var MediaNormalizer
     */
    private $mediaNormalizer;

    /**
     * @var AnalyticsManager
     */
    private $analytics;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param MediaNormalizer $mediaNormalizer
     * @param AnalyticsManager $analytics
     * @param int $cacheTTL
     * @param string $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, MediaNormalizer $mediaNormalizer, AnalyticsManager $analytics, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->mediaNormalizer = $mediaNormalizer;
        $this->analytics = $analytics;
        $this->cache = new FilesystemAdapter('medias', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $slug
     * @return null|Media
     */
    public function get(string $slug): ?Media
    {
        if ($this->cache->hasItem($slug)) {
            $this->analytics->media($slug);
        }
        $media = $this->cache->get($slug, function () use ($slug) {
            $data = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/medias/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                $data = $this->mediaNormalizer->denormalize($data, Media::class);
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $data;
        });


        return $media;
    }

    /**
     * @param string $type
     * @param string $season
     * @param string $designer
     * @param string $look
     * @return null|Media
     */
    public function findByTypeSeasonDesignerLook(string $type, string $season, string $designer, string $look): ?Media
    {
        $media = null;
        if ($type && $season && $designer && $look) {
            $query = compact('type', 'season', 'designer', 'look');
            $cacheKey = 'findByTypeSeasonDesignerLook' . md5(serialize($query));
            $media = $this->cache->get($cacheKey, function () use ($type, $season, $designer, $look) {
                $result = null;
                $apiResponse = $this->apiProvider->request(
                    'GET',
                    sprintf('/api/medias/%s/%s/%s/%s', $type, $season, $designer, $look),
                    [RequestOptions::HTTP_ERRORS => false]
                );
                if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                    $data = json_decode($apiResponse->getBody(), true);
                    $result = $this->mediaNormalizer->denormalize($data, Media::class);
                } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                    $this->logger->error($apiResponse->getBody()->getContents());
                }

                return $result;
            });
        }

        return $media;
    }

    /**
     * @param string $type
     * @param string $season
     * @param string $designer
     * @param string|null $city
     * @return array|mixed
     */
    public function listRelated(string $type, string $season, string $designer, ?string $city = null): array
    {
        $query = array_merge([
            'analytics' => 0,
            'from' => 0,
            'size' => 6
        ], compact('type', 'season', 'designer', 'city'));
        $cacheKey = 'listRelated.' . md5(serialize($query));

        $data = $this->cache->get($cacheKey, function () use ($query) {
            $results = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/medias', [RequestOptions::QUERY => $query, RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $results = json_decode($apiResponse->getBody(), true);
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $results;
        });

        return $data;
    }

    /**
     * @param array $query
     * @param int $from
     * @param int $size
     * @param string $status
     * @return Media[]
     */
    public function list($query = [], $from = 0, $size = self::DEFAULT_SIZE, $status = Status::ENABLED): array
    {
        $query = array_merge($query, compact('from', 'size', 'status'));
        $cacheKey = 'list.' . md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        if ($this->cache->hasItem($cacheKey)) {
            /** @var Media[] $medias */
            $medias = $this->cache->getItem($cacheKey)->get();
            $slugs = [];
            foreach ($medias as $media) {
                $slugs[] = $media->getSlug();
            }
            $analytics = array_merge($query, ['count' => $this->lastCount, 'photos' => implode(',', $slugs)]);
            $this->analytics->page('media_list', $analytics);

            return $medias;
        }

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/medias', [
                RequestOptions::QUERY => $query,
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                foreach ($data as &$datum) {
                    $datum = $this->mediaNormalizer->denormalize($datum, Media::class);
                }
                $this->lastCount = (int)$apiResponse->getHeaderLine('X-Total-Count');
                $countCacheItem = $this->cache->getItem($countCacheKey)->set($this->lastCount);
                $this->cache->save($countCacheItem);
            } elseif ($apiResponse->getStatusCode() === Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE) {
                $this->logger->error($apiResponse->getBody()->getContents());
                $this->lastCount = 0;
                throw new \OutOfRangeException();
            } else {
                $this->lastCount = 0;
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $data;
        });
    }

    /**
     * Find medias looks by model slug
     *
     * @param string $slug
     * @param array $query
     *
     * @return Media[]
     */
    public function listByModel(string $slug, array $query = []): array
    {
        $query = array_merge($query, ['sort' => self::DEFAULT_MEDIAS_MODEL_SORT]);
        $cacheKey = 'listByModel.' . md5(serialize(array_filter(compact('slug', 'query'))));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        if ($this->cache->hasItem($cacheKey)) {
            /** @var Media[] $medias */
            $medias = $this->cache->getItem($cacheKey)->get();
            $slugs = [];
            foreach ($medias as $media) {
                $slugs[] = $media->getSlug();
            }
            $analytics = array_merge($query, ['slug' => $slug, 'count' => $this->lastCount, 'photos' => implode(',', $slugs)]);
            $this->analytics->page('individual_medias_list', $analytics);

            return $medias;
        }

        return $this->cache->get($cacheKey, function () use ($slug, $query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/individuals/' . $slug . '/medias', [
                RequestOptions::QUERY => $query,
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                if (!empty($data)) {
                    foreach ($data as &$datum) {
                        $datum = $this->mediaNormalizer->denormalize($datum, Media::class);
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
}
