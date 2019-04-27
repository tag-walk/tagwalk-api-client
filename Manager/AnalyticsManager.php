<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\Promise;
use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class AnalyticsManager
{
    const EVENT_PAGE = 'page';
    const EVENT_PHOTO_LIST = 'photo_list';
    const EVENT_PHOTO_VIEW = 'photo_view';
    const EVENT_PHOTO_ZOOM = 'photo_zoom';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var bool
     */
    private $enabled;

    /**
     * @param ApiProvider $apiProvider
     * @param LoggerInterface $logger
     * @param bool $enabled
     */
    public function __construct(ApiProvider $apiProvider, LoggerInterface $logger, bool $enabled = true)
    {
        $this->apiProvider = $apiProvider;
        $this->logger = $logger;
        $this->enabled = $enabled;
    }

    /**
     * @param string $slug
     * @param array $query
     * @return bool
     */
    public function media(string $slug, array $query = []): bool
    {
        if ($this->enabled) {
            $response = $this->apiProvider->request('POST', "/api/analytics/media/$slug", [RequestOptions::QUERY => $query]);
            if ($response->getStatusCode() !== Response::HTTP_CREATED) {
                $this->logger->critical('AnalyticsManager::media error: ' . $response->getBody()->getContents());
            }

            return $response === Response::HTTP_CREATED;
        }

        return false;
    }

    /**
     * @param Media[] $medias
     * @param array $query
     */
    public function medias(array $medias, array $query = [])
    {
        if ($this->enabled) {
            $promises = [];
            foreach ($medias as $media) {
                $promises[$media->getSlug()] = $this->apiProvider->requestAsync(
                    'POST',
                    "/api/analytics/media/{$media->getSlug()}",
                    [RequestOptions::QUERY => $query]
                );
            }
            // Wait for the requests to complete, even if some of them fail
            Promise\settle($promises)->wait();
        }
    }

    /**
     * @param string $slug
     * @param array $query
     * @return bool
     */
    public function streetstyle(string $slug, array $query = []): bool
    {
        if ($this->enabled) {
            $response = $this->apiProvider->request('POST', "/api/analytics/streetstyle/$slug", [RequestOptions::QUERY => $query]);
            if ($response->getStatusCode() !== Response::HTTP_CREATED) {
                $this->logger->critical('AnalyticsManager::streetstyle error: ' . $response->getBody()->getContents());
            }

            return $response === Response::HTTP_CREATED;
        }

        return false;
    }

    /**
     * @param Streetstyle[] $streetstyles
     * @param array $query
     */
    public function streetstyles(array $streetstyles, array $query = [])
    {
        if ($this->enabled) {
            $promises = [];
            foreach ($streetstyles as $streetstyle) {
                $promises[$streetstyle->getSlug()] = $this->apiProvider->requestAsync(
                    'POST',
                    "/api/analytics/streetstyle/{$streetstyle->getSlug()}",
                    [RequestOptions::QUERY => $query]
                );
            }
            // Wait for the requests to complete, even if some of them fail
            Promise\settle($promises)->wait();
        }
    }

    /**
     * @param string $route
     * @param array $query
     * @return bool
     */
    public function page(string $route, array $query = []): bool
    {
        if ($this->enabled) {
            $response = $this->apiProvider->request('POST', "/api/analytics/page/$route", [RequestOptions::QUERY => $query]);
            if ($response->getStatusCode() !== Response::HTTP_CREATED) {
                $this->logger->critical('AnalyticsManager::page error: ' . $response->getBody()->getContents());
            }

            return $response === Response::HTTP_CREATED;
        }

        return false;
    }
}
