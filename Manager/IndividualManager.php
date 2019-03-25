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

use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Individual;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class IndividualManager
{
    const DEFAULT_STATUS = 'enabled';
    const DEFAULT_SORT = 'name:asc';

    /**
     * @var ApiProvider
     */
    protected $apiProvider;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var FilesystemAdapter
     */
    protected $cache;

    /**
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('individuals');
    }

    /**
     * @param string $slug
     * @return Individual|null
     */
    public function get(string $slug): ?Individual
    {
        $individual = null;
        $cacheItem = $this->cache->getItem($slug);
        if ($cacheItem->isHit()) {
            $individual = $cacheItem->get();
        } else {
            $query = isset($locale) ? ['language' => $locale] : [];
            $apiResponse = $this->apiProvider->request('GET', '/api/individuals/' . $slug, ['http_errors' => false, 'query' => $query]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $individual = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Individual::class, 'json');
                $cacheItem->set($individual);
                $cacheItem->expiresAfter(86400);
                $this->cache->save($cacheItem);
            }
        }

        return $individual;
    }

    /**
     * @param string|null $language
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @param bool $denormalize
     * @return array|Individual[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 20,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        bool $denormalize = true
    ): array {
        $individuals = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language'));
        $key = md5(serialize(array_merge($query, ['denormalize' => $denormalize])));
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $individuals = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/individuals', ['query' => $query, 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                if ($denormalize) {
                    foreach ($data as $datum) {
                        $individuals[] = $this->serializer->denormalize($datum, Individual::class);
                    }
                } else {
                    $individuals = $data;
                }
                $cacheItem->set($individuals);
                $cacheItem->expiresAfter(3600);
                $this->cache->save($cacheItem);
            }
        }

        return $individuals;
    }

    /**
     * TODO implement count API endpoint
     *
     * @param string $status
     * @return int
     */
    public function count(string $status = self::DEFAULT_STATUS): int
    {
        $count = 0;
        $cacheItem = $this->cache->getItem('count');
        if ($cacheItem->isHit()) {
            $count = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/individuals', ['query' => ['status' => $status, 'size' => 1], 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $count = (int)$apiResponse->getHeaderLine('X-Total-Count');
                $cacheItem->set($count);
                $cacheItem->expiresAfter(3600);
                $this->cache->save($cacheItem);
            }
        }

        return $count;
    }

    /**
     * @param string $prefix
     * @param string|null $language
     * @return array
     */
    public function suggest(
        string $prefix,
        string $language = null
    ): array {
        $individuals = [];
        $query = array_filter(compact('prefix', 'language'));
        $key = md5(serialize($query));
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $individuals = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/individuals/suggestions', ['query' => $query, 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $individuals = json_decode($apiResponse->getBody()->getContents(), true);
                $cacheItem->set($individuals);
                $cacheItem->expiresAfter(3600);
                $this->cache->save($cacheItem);
            }
        }

        return $individuals;
    }
}
