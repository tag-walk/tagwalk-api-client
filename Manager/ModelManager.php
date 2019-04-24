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

use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\Individual;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ModelManager extends IndividualManager
{
    /** @var int last list count */
    public $lastCount;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer $serializer
     * @param int $cacheTTL
     * @param string|null $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, Serializer $serializer, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        parent::__construct($apiProvider, $serializer);
        $this->cache = new FilesystemAdapter('models', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
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
        $cacheKey = md5(serialize($query));

        return $this->cache->get($cacheKey, function () use ($query) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/models/who-walked-the-most', ['query' => $query, 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                if (!empty($data)) {
                    foreach ($data as &$datum) {
                        $datum = $this->serializer->denormalize($datum, Individual::class);
                    }
                }
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
     * @param array $params
     * @return array
     */
    public function listMediasModels(int $size, int $page, array $params = []): array
    {
        $query = array_merge($params, [
            'size' => $size,
            'page' => $page
        ]);
        $cacheKey = md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/models', [
                'query' => $query,
                'http_errors' => false
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
     * @param string $slug
     * @param array $params
     * @return mixed
     */
    public function listMediasModel(string $slug, array $params)
    {
        $cacheKey = md5(serialize(array_filter(compact('slug', 'params'))));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        return $this->cache->get($cacheKey, function () use ($slug, $params, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/individuals/' . $slug . '/medias', ['query' => $params, 'http_errors' => false]);
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

        return $this->cache->get($cacheKey, function () use ($countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/models/new-faces', ['http_errors' => false]);
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
     * @param null|string $designer
     * @param null|string $tags
     * @param string|null $language
     * @return Individual[]
     */
    public function listFilters(
        ?string $type,
        ?string $season,
        ?string $city,
        ?string $designer,
        ?string $tags,
        ?string $language = null
    ): array {
        $models = [];
        $query = array_filter(compact('type', 'season', 'city', 'designer', 'tags', 'language'));
        $key = md5(serialize($query));
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $models = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/models/filter', ['query' => $query, 'http_errors' => false]);
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
