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
use Tagwalk\ApiClientBundle\Model\Designer;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class DesignerManager
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
        $this->cache = new FilesystemAdapter('designers');
    }

    /**
     * @param string $slug
     * @param string $locale
     * @return Designer
     */
    public function get(string $slug, $locale = null): Designer
    {
        $designer = null;
        $key = isset($locale) ? "{$locale}.{$slug}" : $slug;
        $tokenCache = $this->cache->getItem($key);
        if ($tokenCache->isHit()) {
            $designer = $tokenCache->get();
        } else {
            $query = isset($locale) ? ['language' => $locale] : [];
            $apiResponse = $this->apiProvider->request('GET', '/api/designers/' . $slug, ['http_errors' => false, 'query' => $query]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $designer = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Designer::class, 'json');
                $tokenCache->set($designer);
                $tokenCache->expiresAfter(86400);
            }
        }

        return $designer;
    }
}
