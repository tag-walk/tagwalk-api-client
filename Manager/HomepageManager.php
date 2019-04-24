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
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\HomepageSection;

class HomepageManager
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
        $this->cache = new FilesystemAdapter('homepages', $cacheTTL, $cacheDirectory);
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $section
     * @param null|string $language
     * @return Homepage
     */
    public function getBySection(string $section, ?string $language = null): ?Homepage
    {
        if (false === in_array($section, HomepageSection::VALUES)) {
            throw new \InvalidArgumentException('Invalid homepage section argument');
        }
        $key = md5(serialize(compact('section', 'language')));
        $homepage = $this->cache->get($key, function () use ($section, $language) {
            $record = null;
            $apiResponse = $this->apiProvider->request(
                Request::METHOD_GET,
                "/api/homepages/show/{$section}",
                [
                    'query' => ['language' => $language],
                    RequestOptions::HTTP_ERRORS => false
                ]
            );
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Homepage::class, 'json');
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $record;
        });

        return $homepage;
    }

    /**
     * @param string $slug
     * @param null|string $language
     * @return Homepage
     */
    public function get(string $slug, ?string $language = null): ?Homepage
    {
        $key = md5(serialize(compact('slug', 'language')));
        $homepage = $this->cache->get($key, function () use ($slug, $language) {
            $record = null;
            $apiResponse = $this->apiProvider->request(
                Request::METHOD_GET,
                "/api/homepages/{$slug}",
                [
                    'query' => ['language' => $language],
                    RequestOptions::HTTP_ERRORS => false
                ]
            );
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Homepage::class, 'json');
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $record;
        });

        return $homepage;
    }
}
