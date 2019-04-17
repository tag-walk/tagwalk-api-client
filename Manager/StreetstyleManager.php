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
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\StreetstyleNormalizer;

class StreetstyleManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var StreetstyleNormalizer
     */
    private $streetstyleNormalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @param ApiProvider $apiProvider
     * @param StreetstyleNormalizer $streetstyleNormalizer
     * @param int $cacheTTL
     * @param string $cacheDirectory
     */
    public function __construct(ApiProvider $apiProvider, StreetstyleNormalizer $streetstyleNormalizer, int $cacheTTL = 600, string $cacheDirectory = null)
    {
        $this->apiProvider = $apiProvider;
        $this->streetstyleNormalizer = $streetstyleNormalizer;
        $this->cache = new FilesystemAdapter('streetstyles', $cacheTTL, $cacheDirectory);
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
     * @return null|Streetstyle
     */
    public function get(string $slug): ?Streetstyle
    {
        $streetstyle = $this->cache->get($slug, function () use ($slug) {
            $data = null;
            $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                $data = $this->streetstyleNormalizer->denormalize($data, Streetstyle::class);
            } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
                $this->logger->error($apiResponse->getBody()->getContents());
            }

            return $data;
        });

        return $streetstyle;
    }
}
