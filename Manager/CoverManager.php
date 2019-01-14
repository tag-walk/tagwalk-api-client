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

class CoverManager
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
     * @param string $slug
     *
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $slug)
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/covers/' . $slug, ['http_errors' => false]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }
}
