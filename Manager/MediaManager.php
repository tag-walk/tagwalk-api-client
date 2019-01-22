<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Steve valette <steve@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class MediaManager
{
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
     * @param int $from
     * @param int $size
     * @param string|null $type
     * @param string|null $season
     * @param string|null $city
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(
        $from = 0,
        $size = 10,
        ?string $type = null,
        ?string $season = null,
        ?string $city = null
    ) {
        $query = array_filter(compact('from', 'size', 'type', 'season', 'city'));
        $apiResponse = $this->apiProvider->request('GET', '/api/models', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }
}
