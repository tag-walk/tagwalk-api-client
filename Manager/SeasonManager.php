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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Season;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class SeasonManager
{
    const DEFAULT_STATUS = 'enabled';
    const DEFAULT_SORT = 'position:asc';

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
        $this->cache = new FilesystemAdapter('seasons');
    }

    /**
     * @param string|null $language
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @return Season[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 100,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS
    ): array {
        $seasons = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language'));
        $key = serialize($query);
        $tokenCache = $this->cache->getItem($key);
        if ($tokenCache->isHit()) {
            $seasons = $tokenCache->get();
        } else {
            $apiResponse = $this->apiProvider->request('GET', '/api/seasons', ['query' => $query, 'http_errors' => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $seasons = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Season::class, JsonEncoder::FORMAT);
                $tokenCache->set($seasons);
                $tokenCache->expiresAfter(3600);
            }
        }

        return $seasons;
    }
}
