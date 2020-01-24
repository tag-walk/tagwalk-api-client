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
use Tagwalk\ApiClientBundle\Model\News;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class NewsManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'date:desc';
    public const DEFAULT_SIZE = 12;

    /**
     * @var int
     */
    public $lastCount;

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
     * @param null|string       $text
     * @param null|string|array $categories
     * @param null|string       $language
     * @param int               $from
     * @param int               $size
     * @param string            $sort
     * @param string            $status
     *
     * @return News[]
     */
    public function list(
        ?string $text = null,
        $categories = null,
        ?string $language = null,
        $from = 0,
        $size = 10,
        $sort = self::DEFAULT_SORT,
        $status = self::DEFAULT_STATUS
    ): array {
        $data = [];
        $this->lastCount = 0;
        $categories = is_array($categories) ? implode(',', $categories) : $categories;
        $query = array_filter(compact('text', 'categories', 'language', 'from', 'size', 'sort', 'status'));
        $apiResponse = $this->apiProvider->request('GET', '/api/news', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            foreach ($data as $i => $datum) {
                $data[$i] = $this->serializer->denormalize($datum, News::class);
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } else {
            $this->logger->error('NewsManager::list unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param string      $slug
     * @param null|string $language
     *
     * @return News|null
     */
    public function get(string $slug, ?string $language = null): ?News
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET',
            '/api/news/'.$slug,
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => array_filter(['language' => $language]),
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var News $data */
            $data = $this->serializer->deserialize($apiResponse->getBody(), News::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('NewsManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }
}
