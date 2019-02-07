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

class ModelManager
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
     * @param string $type
     * @param string $season
     * @param string $city
     * @param int $length
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function whoWalkedTheMost($type = null, $season = null, $city = null, $length = 10)
    {
        $query = array_filter(compact('type', 'season', 'city', 'length'));
        $apiResponse = $this->apiProvider->request('GET', '/api/models/who-walked-the-most', ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param int $size
     * @param int $page
     * @param array $params
     * @return array
     */
    public function listMediasModels(int $size, int $page, array $params = []): array
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/models', [
            'query' => array_merge($params, [
                'size' => $size,
                'page' => $page
            ]),
            'http_errors' => false
        ]);

        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param int $size
     * @param int $page
     * @param array $params
     * @return int
     */
    public function countListMediasModels(int $size, int $page, array $params = [])
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/models', [
            'query' => array_merge($params, [
                'size' => $size,
                'page' => $page
            ]),
            'http_errors' => false
        ]);

        $count = $apiResponse->getHeader('X-Total-Count');

        return isset($count[0]) ? $count[0] : 0;

    }
}
