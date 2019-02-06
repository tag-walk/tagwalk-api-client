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

use GuzzleHttp\RequestOptions;
use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\Page;
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
     * @var Serializer
     */
    private $serializer;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer $serializer
     */
    public function __construct(ApiProvider $apiProvider, Serializer $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param int $from
     * @param int $size
     * @param string $sort
     * @param string $status
     * @param null|string $name
     * @param null|string $text
     * @return Page[]
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function list(
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        ?string $name = null,
        ?string $text = null
    ): array {
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'name', 'text'));
        $apiResponse = $this->apiProvider->request('GET', '/api/page', ['query' => $query]);
        $data = json_decode($apiResponse->getBody(), true);
        $pages = [];
        foreach ($data as $datum) {
            $pages[] = $this->serializer->denormalize($datum, Page::class);
        }

        return $pages;
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
     * @param null|string $language
     * @return Page
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $slug, ?string $language = null): Page
    {
        $params = [];
        if (isset($language)) {
            $params['query'] = ['language' => $language];
        }
        $apiResponse = $this->apiProvider->request('GET', '/api/page/' . $slug, $params);
        $data = json_decode($apiResponse->getBody(), true);
        $page = $this->serializer->denormalize($data, Page::class);

        return $page;
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
     * @param Page $record
     * @return Page
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Page $record): Page
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($record, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('POST', '/api/page', $params);
        $data = json_decode($apiResponse->getBody(), true);
        $page = $this->serializer->denormalize($data, Page::class);

        return $page;
    }

    /**
     * @param string $slug
     * @param Page $record
     * @return Page
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function update(string $slug, Page $record): Page
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($record, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('PUT', '/api/page/' . $slug, $params);
        $data = json_decode($apiResponse->getBody(), true);
        $page = $this->serializer->denormalize($data, Page::class);

        return $page;
    }
}
