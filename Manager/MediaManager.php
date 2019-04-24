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

class MediaManager
{
    /** @var int default list size */
    const DEFAULT_SIZE = 24;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @param ApiProvider $apiProvider
     * @param MediaNormalizer $mediaNormalizer
     * @param string $cacheDirectory
     * @param int $cacheTTL
     */
    public function __construct(ApiProvider $apiProvider, MediaNormalizer $mediaNormalizer, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->mediaNormalizer = $mediaNormalizer;
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
            $media = $this->cache->get(md5(serialize($query)), function () use ($type, $season, $designer, $look) {
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
        $cacheKey = md5(serialize($query));

        $data = $this->cache->get($cacheKey, function () use ($query) {
            $results = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/medias', ['query' => $query, 'http_errors' => false]);
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
     * @return Media[]
     */
    public function list($query = [], $from = 0, $size = self::DEFAULT_SIZE)
    {
        $query = array_merge($query, compact('from', 'size'));
        $cacheKey = md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/medias', [
                'query' => $query,
                'http_errors' => false
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
}
