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
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class TrendManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'created_at:desc';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var int
     */
    private $lastCount;

    /**
     * @param ApiProvider $apiProvider
     */
    public function __construct(ApiProvider $apiProvider)
    {
        $this->apiProvider = $apiProvider;
        $this->logger = new NullLogger();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param null|string $type
     * @param null|string $season
     * @param null|string $city
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     *
     * @return array
     */
    public function list(
        ?string $type = null,
        ?string $season = null,
        ?string $city = null,
        $from = 0,
        $size = 10,
        $sort = self::DEFAULT_SORT,
        $status = self::DEFAULT_STATUS
    ): array {
        $data = [];
        $this->lastCount = 0;
        $query = array_filter(compact('type', 'season', 'city', 'from', 'size', 'sort', 'status'));
        $apiResponse = $this->apiProvider->request('GET', '/api/trends', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } else {
            $this->logger->error('TrendManager::findBy unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param string $slug
     *
     * @return array|null array representation of a trend
     */
    public function get(string $slug): ?array
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/trends/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('TrendManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param string      $type
     * @param string      $season
     * @param null|string $city
     *
     * @return array list the trends of the season
     */
    public function findBy(string $type, string $season, ?string $city = null): array
    {
        $data = [];
        $query = array_filter(compact('city'));
        $apiResponse = $this->apiProvider->request('GET', "/api/trends/$type/$season", [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('TrendManager::findBy unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }
}
