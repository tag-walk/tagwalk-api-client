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
    public const EVENT_PAGE = 'page';
    public const EVENT_PHOTO_LIST = 'photo_list';
    public const EVENT_PHOTO_VIEW = 'photo_view';
    public const EVENT_PHOTO_ZOOM = 'photo_zoom';

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
     * @param ApiProvider     $apiProvider
     * @param LoggerInterface $logger
     * @param bool            $enabled
     */
    public function __construct(ApiProvider $apiProvider, LoggerInterface $logger, bool $enabled = true)
    {
        $this->apiProvider = $apiProvider;
        $this->logger = $logger;
        $this->enabled = $enabled;
    }

    /**
     * @param string      $slug
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool
     */
    public function media(string $slug, array $query = [], ?string $clientIp = null): bool
    {
        if ($this->enabled) {
            $response = $this->apiProvider->request('POST',
                "/api/analytics/media/$slug",
                [
                    RequestOptions::QUERY   => $query,
                    RequestOptions::HEADERS => ['X-Client-IP' => $clientIp],
                ]
            );
            $status = $response->getStatusCode();
            $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
            if (!$success) {
                $this->logger->error('AnalyticsManager::media',
                    [
                        RequestOptions::QUERY => $query,
                        'message'             => $response->getBody()->getContents(),
                    ]);
            }

            return $success;
        }

        return false;
    }

    /**
     * @param Media[] $medias
     * @param array $query
     */
    public function medias(array $medias, array $query = []): void
    {
        if ($this->enabled) {
            $promises = [];
            foreach ($medias as $media) {
                $slug = $media->getSlug();
                $promises[$slug] = $this->apiProvider->requestAsync(
                    'POST',
                    "/api/analytics/media/{$slug}",
                    [RequestOptions::QUERY => $query]
                );
            }
            // Wait for the requests to complete, even if some of them fail
            Promise\settle($promises)->wait();
        }
    }

    /**
     * @param string      $slug
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool
     */
    public function streetstyle(string $slug, array $query = [], ?string $clientIp = null): bool
    {
        if ($this->enabled) {
            $response = $this->apiProvider->request('POST',
                "/api/analytics/streetstyle/$slug",
                [
                    RequestOptions::QUERY   => $query,
                    RequestOptions::HEADERS => ['X-Client-IP' => $clientIp],
                ]
            );
            $status = $response->getStatusCode();
            $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
            if (!$success) {
                $this->logger->error('AnalyticsManager::streetstyle',
                    [
                        RequestOptions::QUERY => $query,
                        'message'             => $response->getBody()->getContents(),
                    ]);
            }

            return $success;
        }

        return false;
    }

    /**
     * @param Streetstyle[] $streetstyles
     * @param array $query
     */
    public function streetstyles(array $streetstyles, array $query = []): void
    {
        if ($this->enabled) {
            $promises = [];
            foreach ($streetstyles as $streetstyle) {
                $slug = $streetstyle->getSlug();
                $promises[$slug] = $this->apiProvider->requestAsync(
                    'POST',
                    "/api/analytics/streetstyle/{$slug}",
                    [RequestOptions::QUERY => $query]
                );
            }
            // Wait for the requests to complete, even if some of them fail
            Promise\settle($promises)->wait();
        }
    }

    /**
     * @param string      $route
     * @param array       $query
     * @param string|null $clientIp
     *
     * @return bool success
     */
    public function page(string $route, array $query = [], ?string $clientIp = null): bool
    {
        if ($this->enabled) {
            $response = $this->apiProvider->request('POST',
                "/api/analytics/page/$route",
                [
                    RequestOptions::QUERY   => $query,
                    RequestOptions::HEADERS => ['X-Client-IP' => $clientIp],
                ]
            );
            $status = $response->getStatusCode();
            $success = $status === Response::HTTP_CREATED || $status === Response::HTTP_NO_CONTENT;
            if (!$success) {
                $this->logger->error('AnalyticsManager::page',
                    [
                        RequestOptions::QUERY => $query,
                        'message'             => $response->getBody()->getContents(),
                    ]
                );
            }

            return $success;
        }

        return false;
    }
}
