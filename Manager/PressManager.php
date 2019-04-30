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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Model\Press;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\PressNormalizer;

class PressManager
{
    /**
     * @var int last query result count
     */
    public $lastCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var PressNormalizer
     */
    private $pressNormalizer;

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
     * @param PressNormalizer $pressNormalizer
     * @param AnalyticsManager $analytics
     * @param int $cacheTTL
     * @param string|null $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, PressNormalizer $pressNormalizer, AnalyticsManager $analytics, int $cacheTTL = 3600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->pressNormalizer = $pressNormalizer;
        $this->analytics = $analytics;
        $this->cache = new FilesystemAdapter('press', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $query
     * @return Press[]
     */
    public function list(array $query): array
    {
        $cacheKey = 'list.' . md5(serialize($query));
        $countCacheKey = "count.$cacheKey";
        $this->lastCount = $this->cache->getItem($countCacheKey)->get();

        if ($this->cache->hasItem($cacheKey)) {
            $this->analytics->page('press_list', array_merge($query, ['count' => $this->lastCount]));
        }

        return $this->cache->get($cacheKey, function () use ($query, $countCacheKey) {
            $results = [];
            $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/press', ['query' => $query, RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $results[] = $this->pressNormalizer->denormalize($datum, Press::class);
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

            return $results;
        });
    }
}
