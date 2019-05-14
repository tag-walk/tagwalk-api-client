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
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\News;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class NewsManager
{
    const DEFAULT_STATUS = 'enabled';
    const DEFAULT_SORT = 'created_at:desc';
    const DEFAULT_SIZE = 12;
    /**
     * @var int
     */
    public $lastCount;
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
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     * @param AnalyticsManager $analytics
     * @param int $cacheTTL
     * @param string|null $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer, AnalyticsManager $analytics, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->analytics = $analytics;
        $this->cache = new FilesystemAdapter('news', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param null|string $text
     * @param null|string|array $categories
     * @param null|string $language
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @return News[]
     */
    public function list(
        ?string $text = null,
        $categories = null,
        ?string $language = null,
        $from = 0,
        $size = 10,
        $sort = self::DEFAULT_SORT,
        $status = self::DEFAULT_STATUS
    ): array
    {
        $categories = isset($categories) && is_array($categories) ? implode($categories, ',') : $categories;
        $query = array_filter(compact('text', 'categories', 'language', 'from', 'size', 'sort', 'status'));
        $cacheKey = 'list.' . md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        if ($this->cache->hasItem($cacheKey)) {
            $this->cache->delete($cacheKey);
            $analytics = array_merge($query, ['count' => $this->lastCount]);
            $this->analytics->page('news_list', $analytics);
        }

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $data = [];
            $apiResponse = $this->apiProvider->request('GET', '/api/news', [RequestOptions::QUERY => $query, RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                foreach ($data as &$datum) {
                    $datum = $this->serializer->denormalize($datum, News::class);
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
     * @param null|string $language
     * @return News|null
     */
    public function get(string $slug, ?string $language = null): ?News
    {
        if ($this->cache->hasItem($slug)) {
            $this->analytics->page('news_show', compact('slug', 'language'));
        }

        return $this->cache->get($slug, function () use ($slug, $language) {
            $data = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/news/' . $slug, [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY => array_filter(['language' => $language])
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = $this->serializer->deserialize($apiResponse->getBody(), News::class, JsonEncoder::FORMAT);
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $data;
        });
    }
}
