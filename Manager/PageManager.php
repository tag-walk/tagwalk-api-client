<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Page;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class PageManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'created_at:desc';
    public const DEFAULT_SIZE = 10;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider         $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
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
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     * @param null|string $name
     * @param null|string $text
     *
     * @return Page[]
     */
    public function list(
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        ?string $name = null,
        ?string $text = null
    ): array {
        $pages = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'name', 'text'));
        $apiResponse = $this->apiProvider->request('GET', '/api/page', [RequestOptions::QUERY => $query]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $pages = [];
            foreach ($data as $datum) {
                $pages[] = $this->serializer->denormalize($datum, Page::class);
            }
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('PageManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $pages;
    }

    /**
     * @param string      $status
     * @param null|string $name
     * @param null|string $text
     *
     * @return int
     */
    public function count(
        string $status = self::DEFAULT_STATUS,
        ?string $name = null,
        ?string $text = null
    ): int {
        $query = array_filter(compact('status', 'name', 'text'));
        $apiResponse = $this->apiProvider->request('GET', '/api/page', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            $this->logger->error('PageManager::count unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return (int) $apiResponse->getHeaderLine('X-Total-Count');
    }

    /**
     * @param string      $slug
     * @param null|string $language
     *
     * @return Page|null
     */
    public function get(string $slug, ?string $language = null): ?Page
    {
        $page = null;
        $query = compact('language');
        $apiResponse = $this->apiProvider->request(
            'GET',
            '/api/page/' . $slug,
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => $query,
            ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $page = $this->serializer->denormalize($data, Page::class);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('PageManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $page;
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE',
            '/api/page/' . $slug,
            [
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() !== Response::HTTP_NO_CONTENT) {
            $this->logger->error('PageManager::delete unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);

            return false;
        }

        return true;
    }

    /**
     * @param Page $record
     *
     * @return Page
     */
    public function create(Page $record): Page
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($record, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('POST', '/api/page', $params);
        $data = json_decode($apiResponse->getBody(), true);

        return $this->serializer->denormalize($data, Page::class);
    }

    /**
     * @param string $slug
     * @param Page   $record
     *
     * @return Page
     */
    public function update(string $slug, Page $record): Page
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($record, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('PUT', '/api/page/' . $slug, $params);
        $data = json_decode($apiResponse->getBody(), true);

        return $this->serializer->denormalize($data, Page::class);
    }
}
