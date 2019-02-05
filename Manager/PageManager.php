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

class PageManager
{
    const DEFAULT_STATUS = 'enabled';
    const DEFAULT_SORT = 'created_at:desc';
    const DEFAULT_SIZE = 10;

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
     * @param string $sort
     * @param string $status
     * @param null|string $name
     * @param null|string $text
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        ?string $name = null,
        ?string $text = null
    ) {
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'name', 'text'));
        $apiResponse = $this->apiProvider->request('GET', '/api/page', ['query' => $query]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param string $status
     * @param null|string $name
     * @param null|string $text
     * @return int
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function count(
        string $status = self::DEFAULT_STATUS,
        ?string $name = null,
        ?string $text = null
    ) {
        $query = array_filter(compact('status', 'name', 'text'));
        $apiResponse = $this->apiProvider->request('GET', '/api/page', ['query' => $query]);

        return (int)$apiResponse->getHeader('X-Total-Count')[0];
    }

    /**
     * @param string $slug
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $slug): array
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/page/' . $slug);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param string $slug
     * @return bool
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', '/api/page/' . $slug);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param array $record
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(array $record): array
    {
        $query = ['body' => $record];
        $apiResponse = $this->apiProvider->request('POST', '/api/page', ['query' => $query]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }

    /**
     * @param string $slug
     * @param array $record
     * @return array
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $slug, array $record): array
    {
        $query = ['body' => $record];
        $apiResponse = $this->apiProvider->request('PUT', '/api/pages/' . $slug, ['query' => $query]);
        $data = json_decode($apiResponse->getBody(), true);

        return $data;
    }
}
