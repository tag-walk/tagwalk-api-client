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

use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class TrendManager
{
    const DEFAULT_STATUS = 'ALL';
    const DEFAULT_SORT = 'created_at:desc';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @param ApiProvider $apiProvider
     */
    public function __construct(ApiProvider $apiProvider)
    {
        $this->apiProvider = $apiProvider;
    }

    /**
     * @param null|string $type
     * @param null|string $season
     * @param null|string $city
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @return mixed
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(
        ?string $type = null,
        ?string $season = null,
        ?string $city = null,
        $from = 0,
        $size = 10,
        $sort = self::DEFAULT_SORT,
        $status = self::DEFAULT_STATUS
    ) {
        $query = array_filter(compact('type', 'season', 'city', 'from', 'size', 'sort', 'status'));
        $apiResponse = $this->apiProvider->request('GET', '/api/trends', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param string $slug
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $slug)
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/trends/' . $slug, ['http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }
}
