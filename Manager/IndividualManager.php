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
use Tagwalk\ApiClientBundle\Model\Individual;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class IndividualManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'name:asc';
    public const DEFAULT_MODEL = 'true';

    /**
     * @var ApiProvider
     */
    protected $apiProvider;

    /**
     * @var Serializer
     */
    protected $serializer;

    /**
     * @var int
     */
    public $lastCount;

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
     * @param string      $slug
     * @param null|string $language
     *
     * @return Individual|null
     */
    public function get(string $slug, ?string $language = null): ?Individual
    {
        $individual = null;
        $query = array_filter(compact('language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/'.$slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Individual $individual */
            $individual = $this->serializer->deserialize((string) $apiResponse->getBody(), Individual::class, JsonEncoder::FORMAT);
        }

        return $individual;
    }

    /**
     * @param string|null $language
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     * @param string      $model
     * @param bool        $denormalize
     *
     * @return array|Individual[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 20,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        bool $denormalize = true,
        string $model = self::DEFAULT_MODEL
    ): array {
        $individuals = [];
        $this->lastCount = 0;
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language', 'model'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            if ($denormalize) {
                foreach ($data as $datum) {
                    $individuals[] = $this->serializer->denormalize($datum, Individual::class);
                }
            } else {
                $individuals = $data;
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $individuals;
    }

    /**
     * @param string $status
     *
     * @return int
     */
    public function count(string $status = self::DEFAULT_STATUS): int
    {
        $count = 0;
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals', [
            RequestOptions::QUERY       => [
                'status' => $status,
                'size'   => 1,
            ],
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $count = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $count;
    }

    /**
     * @param string      $prefix
     * @param string|null $language
     *
     * @return array
     */
    public function suggest(string $prefix, string $language = null): array
    {
        $individuals = [];
        $query = array_filter(compact('prefix', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/suggestions', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $individuals = json_decode((string) $apiResponse->getBody(), true);
        }

        return $individuals;
    }

    /**
     * @param string|null $city
     * @param string|null $season
     * @param string|null $designers
     * @param string|null $tags
     * @param string|null $language
     *
     * @return Individual[]
     */
    public function listFiltersStreet(
        ?string $city,
        ?string $season,
        ?string $designers,
        ?string $tags,
        ?string $language = null
    ): array {
        $results = [];
        $query = array_filter(compact('city', 'season', 'designers', 'tags', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/filter/streetstyle', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, Individual::class);
            }
        }

        return $results;
    }
}
