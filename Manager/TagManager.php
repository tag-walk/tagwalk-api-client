<?php
/**
 * PHP version 7.
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
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Tag;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class TagManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'name:asc';

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
     * @var int
     */
    public $lastCount;

    /**
     * @param ApiProvider          $apiProvider
     * @param SerializerInterface  $serializer
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $slug
     * @param string $locale
     *
     * @return Tag|null
     */
    public function get(string $slug, $locale = null): ?Tag
    {
        $tag = null;
        $query = isset($locale) ? ['language' => $locale] : [];
        $apiResponse = $this->apiProvider->request('GET', '/api/tags/'.$slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Tag $tag */
            $tag = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Tag::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('TagManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $tag;
    }

    /**
     * @param string|null $language
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     * @param bool        $denormalize
     *
     * @return array|Tag[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 20,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        bool $denormalize = true
    ): array {
        $tags = [];
        $this->lastCount = 0;
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/tags', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody()->getContents(), true);
            if ($denormalize) {
                foreach ($data as $datum) {
                    $tags[] = $this->serializer->denormalize($datum, Tag::class);
                }
            } else {
                $tags = $data;
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } else {
            $this->logger->error('TagManager::list unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $tags;
    }

    /**
     * @param string $status
     *
     * @return int
     */
    public function count(string $status = self::DEFAULT_STATUS): int
    {
        $count = 0;
        $apiResponse = $this->apiProvider->request('GET', '/api/tags', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => [
                'status' => $status,
                'size'   => 1,
            ],
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $count = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } else {
            $this->logger->error('TagManager::count unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $count;
    }

    /**
     * @param string      $prefix
     * @param string|null $language
     *
     * @return array
     */
    public function suggest(
        string $prefix,
        string $language = null
    ): array {
        $tags = [];
        $query = array_filter(compact('prefix', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/tags/suggestions', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $tags = json_decode($apiResponse->getBody()->getContents(), true);
        } else {
            $this->logger->warning('TagManager::suggest unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $tags;
    }
}
