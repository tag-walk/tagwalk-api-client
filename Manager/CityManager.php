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
use Tagwalk\ApiClientBundle\Model\City;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CityManager
{
    const DEFAULT_STATUS = 'enabled';
    const DEFAULT_SORT = 'name:asc';

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
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('cities');
    }

    /**
     * @param string|null $language
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @return City[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 100,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS
    ): array {
        $cities = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language'));
        $key = md5(serialize($query));
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $cities = $cacheItem->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/cities', ['query' => $query, 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $cities[] = $this->serializer->denormalize($datum, City::class);
                }
                $cacheItem->set($cities);
                $cacheItem->expiresAfter(86400);
                $this->cache->save($cacheItem);
            }
        }

        return $cities;
    }
}
