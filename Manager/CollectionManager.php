<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Collection;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CollectionManager
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
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @var AnalyticsManager
     */
    private $analytics;

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
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer,
        AnalyticsManager $analytics,
        int $cacheTTL = 600,
        string $cacheDirectory = null
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->analytics = $analytics;
        $this->cache = new FilesystemAdapter('collections', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $type
     * @param string $designer
     * @param string $season
     * @return null|Collection
     */
    public function find(string $type, string $designer, string $season): ?Collection
    {
        $params = compact('type', 'designer', 'season');
        $cacheKey = md5('find.' . serialize($params));
        if ($this->cache->hasItem($cacheKey)) {
            $this->analytics->page('collection_find_by_type_designer_season', $params);
        }
        $collection = $this->cache->get($cacheKey, function () use ($type, $designer, $season) {
            $data = null;
            $apiResponse = $this->apiProvider->request(
                'GET',
                sprintf('/api/collections/%s/%s/%s', $type, $designer, $season),
                [RequestOptions::HTTP_ERRORS => false]
            );
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                $data = $this->serializer->denormalize($data, Collection::class);
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $data;
        });

        return $collection;
    }
}
