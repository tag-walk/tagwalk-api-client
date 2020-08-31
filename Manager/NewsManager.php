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
    public const SIMILAR_DEFAULT_SIZE = 2;

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
     * @param ApiProvider          $apiProvider
     * @param SerializerInterface  $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * $param['text']       = (string) The search text to return matching news
     * $param['categories'] = (string|array) The categories to filter news
     * $param['individual'] = (string) The individual slug to filter news attached to her/him
     * $param['locale']     = (string) The application locale.
     *
     * @param array  $params
     * @param int    $from
     * @param int    $size
     * @param string $sort
     * @param string $status
     *
     * @return News[]
     */
    public function list(
        array $params = [],
        int $from = 0,
        int $size = 10,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS
    ): array {
        $data = [];
        $this->lastCount = 0;
        if (isset($params['categories'])) {
            $params['categories'] = is_array($params['categories']) ? implode(',', $params['categories']) : $params['categories'];
        }
        $query = compact('from', 'size', 'sort', 'status');
        $query = array_filter(array_merge($query, $params));
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
        $apiResponse = $this->apiProvider->request(
            'GET',
            '/api/news/'.$slug,
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => array_filter(['language' => $language]),
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var News $data */
            $data = $this->serializer->deserialize($apiResponse->getBody(), News::class, JsonEncoder::FORMAT);
        }

        return $data;
    }

    public function getWithSimilar(
        string $slug,
        int $similarSize = self::SIMILAR_DEFAULT_SIZE,
        ?string $language = null
    ): array {
        $apiResponse = $this->apiProvider->request('GET', '/api/news/similar/' . $slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => array_filter(['language' => $language, 'size' => $similarSize]),
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return [];
        }

        $data = json_decode($apiResponse->getBody(), true);
        $data['news'] = $this->serializer->denormalize($data['news'], News::class, JsonEncoder::FORMAT);

        foreach ($data['similar'] as $index => $similar) {
            $data['similar'][$index] = $this->serializer->denormalize($similar, News::class, JsonEncoder::FORMAT);
        }

        return $data;
    }
}
