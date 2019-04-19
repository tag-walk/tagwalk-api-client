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
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Seller;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class SellerManager
{
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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     * @param int $cacheTTL
     * @param string $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('sellers', $cacheTTL, $cacheDirectory);
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
     * @param null|string $language
     * @return Seller
     */
    public function get(string $slug, ?string $language = null): ?Seller
    {
        $key = md5(serialize(compact('slug', 'language')));
        $seller = $this->cache->get($key, function () use ($slug, $language) {
            $record = null;
            $apiResponse = $this->apiProvider->request(
                Request::METHOD_GET,
                "/api/sellers/{$slug}",
                [
                    'query' => ['language' => $language],
                    RequestOptions::HTTP_ERRORS => false
                ]
            );
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Seller::class, 'json');
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $record;
        });

        return $seller;
    }

    /**
     * @param null|string $language
     * @param int|null $from
     * @param int|null $size
     * @param null|string $sort
     * @param null|string $status
     * @return Seller[]
     */
    public function list(?string $language = null, ?int $from = 0, ?int $size = 100, ?string $sort = self::DEFAULT_SORT, ?string $status = Status::ENABLED): array
    {
        $query = compact('from', 'size', 'language', 'sort', 'status');
        $key = md5(serialize($query));
        $sellers = $this->cache->get($key, function () use ($query) {
            $records = [];
            $apiResponse = $this->apiProvider->request(
                Request::METHOD_GET,
                '/api/sellers',
                [
                    'query' => $query,
                    RequestOptions::HTTP_ERRORS => false
                ]
            );
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody()->getContents(), true);
                foreach ($data as $datum) {
                    $records[] = $this->serializer->denormalize($datum, Seller::class);
                }
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $records;
        });

        return $sellers;
    }
}
