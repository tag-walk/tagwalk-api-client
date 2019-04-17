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
use Tagwalk\ApiClientBundle\Model\Gallery;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\GalleryNormalizer;

class GalleryManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var GalleryNormalizer
     */
    private $galleryNormalizer;

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
     * @param GalleryNormalizer $galleryNormalizer
     * @param int $cacheTTL
     * @param string $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, GalleryNormalizer $galleryNormalizer, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->galleryNormalizer = $galleryNormalizer;
        $this->cache = new FilesystemAdapter('galleries', $cacheTTL, $cacheDirectory);
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
     * @param array $params
     * @param bool $denormalize
     * @return null|array|Gallery
     */
    public function get(string $slug, array $params = [], bool $denormalize = false)
    {
        $gallery = $this->cache->get($slug, function () use ($slug, $params, $denormalize) {
            $data = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/galleries/' . $slug, ['query' => $params, RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return null;
            } elseif ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                if ($denormalize) {
                    $data = $this->galleryNormalizer->denormalize($data, Gallery::class);
                }
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $data;
        });

        return $gallery;
    }

    /**
     * @param string $slug
     * @return int|null
     */
    public function count(string $slug)
    {
        $count = $this->cache->get("count.$slug", function () use ($slug) {
            $count = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/galleries/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
                return null;
            } elseif ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                $count = count($data['streetstyles']);
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $count;
        });

        return $count;
    }
}
