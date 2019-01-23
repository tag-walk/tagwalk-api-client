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
     * @param int $from
     * @param int $size
     * @param string|null $type
     * @param string|null $season
     * @param string|null $city
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function listModelsMedia(
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

    /**
     * @param int $from
     * @param int $size
     * @param string|null $type
     * @param string|null $season
     * @param string|null $city
     *
     * @param string|null $slug
     * @return array
     */
    public function listModelsMediaByName(
        $from = 0,
        $size = 10,
        ?string $type = null,
        ?string $season = null,
        ?string $city = null,
        ?string $slug = null
    ) {
        $query = array_filter(compact('from', 'size', 'type', 'season', 'city'));
        $apiResponse = $this->apiProvider->request('GET', '/api/models/' . $slug, ['query' => $query, 'http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }
}
