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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Cover;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CoverManager
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
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     * @param int $cacheTTL
     * @param string|null $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer, int $cacheTTL = 3600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('covers', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param string $slug
     *
     * @return Cover
     */
    public function get(string $slug)
    {
        return $this->cache->get($slug, function () use ($slug) {
            $cover = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/covers/' . $slug, [
                RequestOptions::HTTP_ERRORS => false
            ]);
            if (Response::HTTP_OK === $apiResponse->getStatusCode()) {
                $cover = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Cover::class, 'json');
            }

            return $cover;
        });
    }
}
