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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Live;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class LiveManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'position:asc';
    public const DEFAULT_SIZE = 10;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     * @param int $cacheTTL
     * @param string|null $cacheDirectory
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer,
        int $cacheTTL = 600,
        string $cacheDirectory = null
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('live', $cacheTTL, $cacheDirectory);
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
     * @return Live|null
     */
    public function get(string $slug): ?Live
    {
        $live = null;
        $cacheItem = $this->cache->getItem($slug);
        if ($cacheItem->isHit()) {
            $live = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', "/api/live/{$slug}", [RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $live = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Live::class, 'json');
                $cacheItem->set($live);
                $this->cache->save($cacheItem);
            }
        }

        return $live;
    }

    /**
     * @param null|string $type
     * @param null|string $season slug
     * @param null|string $city
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @param bool $denormalize
     * @return array|Live[]
     */
    public function list(
        ?string $type = null,
        ?string $season = null,
        ?string $city = null,
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        bool $denormalize = true
    ): array {
        $lives = [];
        $query = array_filter(compact('type', 'season', 'city', 'from', 'size', 'sort', 'status'));
        $key = 'list.' . md5(serialize(array_merge($query, ['denormalize' => $denormalize])));
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $lives = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/live', [
                RequestOptions::QUERY => $query,
                RequestOptions::HTTP_ERRORS => false
            ]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                if ($denormalize) {
                    foreach ($data as $datum) {
                        $lives[] = $this->serializer->denormalize($datum, Live::class);
                    }
                } else {
                    $lives = $data;
                }
                $cacheItem->set($lives);
                $this->cache->save($cacheItem);
            }
        }

        return $lives;
    }
}
