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
use Tagwalk\ApiClientBundle\Model\Tag;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class TagManager
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
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->cache = new FilesystemAdapter('tags');
    }

    /**
     * @param string $slug
     * @param string $locale
     * @return Tag
     */
    public function get(string $slug, $locale = null): Tag
    {
        $tag = null;
        $key = isset($locale) ? "{$locale}.{$slug}" : $slug;
        $cacheItem = $this->cache->getItem($key);
        if ($cacheItem->isHit()) {
            $tag = $cacheItem->get();
        } else {
            $query = isset($locale) ? ['language' => $locale] : [];
            $apiResponse = $this->apiProvider->request('GET', '/api/tags/' . $slug, ['http_errors' => false, 'query' => $query]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $tag = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Tag::class, 'json');
                $cacheItem->set($tag);
                $cacheItem->expiresAfter(86400);
                $this->cache->save($cacheItem);
            }
        }

        return $tag;
    }
}
