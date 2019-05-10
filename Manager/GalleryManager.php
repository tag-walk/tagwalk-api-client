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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Gallery;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class GalleryManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

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
     * @var int
     */
    public $lastCount;

    /**
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     * @param AnalyticsManager $analytics
     * @param int $cacheTTL
     * @param string $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer, AnalyticsManager $analytics, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->analytics = $analytics;
        $this->cache = new FilesystemAdapter('galleries', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $slug
     * @param array $query
     * @return null|array|Gallery
     */
    public function get(string $slug, array $query = [])
    {
        $cacheKey = 'get.' . md5(serialize(compact('slug', 'query')));
        $countCacheKey = "count.$cacheKey";

        if ($this->cache->hasItem($cacheKey) && $this->cache->hasItem($countCacheKey)) {
            /** @var Gallery $gallery */
            $gallery = $this->cache->getItem($cacheKey)->get();
            $slugs = [];
            $this->lastCount = $this->cache->getItem($countCacheKey)->get();
            if (null !== $gallery && count($gallery->getStreetstyles())) {
                foreach ($gallery->getStreetstyles() as $streetstyle) {
                    $slugs[] = $streetstyle->getSlug();
                }
            }
            $analytics = array_merge($query, ['slug' => $slug, 'count' => $this->lastCount, 'photos' => implode(',', $slugs)]);
            $this->analytics->page('gallery_show', $analytics);

            return $gallery;
        }

        return $this->cache->get($cacheKey, function () use ($slug, $query, $countCacheKey) {
            $data = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/galleries/' . $slug, [
                RequestOptions::QUERY => $query,
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Gallery::class, JsonEncoder::FORMAT);
                $this->lastCount = (int)$apiResponse->getHeaderLine('X-Total-Count');
                $this->cache->save($this->cache->getItem($countCacheKey)->set($this->lastCount));
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
                $this->lastCount = 0;
            }

            return $data;
        });
    }
}
