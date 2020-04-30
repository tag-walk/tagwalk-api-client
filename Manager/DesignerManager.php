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
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Exception\ApiServerErrorException;
use Tagwalk\ApiClientBundle\Model\Designer;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class DesignerManager
{
    public const DEFAULT_SIZE = 24;
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
     * @var int
     */
    public $lastQueryCount;

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
     * @param string $slug
     * @param string $language
     *
     * @return Designer|null
     */
    public function get(string $slug, $language = null): ?Designer
    {
        $data = null;
        $query = array_filter(compact('language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/designers/'.$slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Designer $data */
            $data = $this->serializer->deserialize((string) $apiResponse->getBody(), Designer::class, 'json');
        }

        return $data;
    }

    /**
     * @param string|null $language
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     * @param bool        $talent
     * @param bool        $tagbook
     * @param bool        $denormalize
     * @param string|null $country
     *
     * @return array|Designer[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 20,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        bool $talent = false,
        bool $tagbook = false,
        bool $denormalize = true,
        ?string $country = null
    ): array {
        $designers = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language', 'talent', 'tagbook', 'country'));
        $apiResponse = $this->apiProvider->request('GET', '/api/designers', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            if ($denormalize) {
                foreach ($data as $datum) {
                    $designers[] = $this->serializer->denormalize($datum, Designer::class);
                }
            } else {
                $designers = $data;
            }
            $this->lastQueryCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            throw new ApiServerErrorException($apiResponse->getBody());
        }

        return $designers;
    }

    /**
     * @param string $status
     *
     * @return int
     */
    public function count(string $status = self::DEFAULT_STATUS): int
    {
        $count = 0;
        $apiResponse = $this->apiProvider->request('GET', '/api/designers', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => [
                'status' => $status,
                'size'   => 1,
            ],
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
    public function suggest(
        string $prefix,
        string $language = null
    ): array {
        $designers = [];
        $query = array_filter(compact('prefix', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/designers/suggestions', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $designers = json_decode((string) $apiResponse->getBody(), true);
        }

        return $designers;
    }

    /**
     * @param null|string $type
     * @param null|string $season
     * @param null|string $city
     * @param null|string $tags
     * @param null|string $models
     * @param bool|null   $talent
     * @param string|null $language
     * @param string|null $country
     *
     * @return array
     */
    public function listFilters(
        ?string $type,
        ?string $season,
        ?string $city,
        ?string $tags,
        ?string $models,
        ?bool $talent = false,
        ?string $language = null,
        ?string $country = null
    ): array {
        $results = [];
        $query = array_filter(compact('type', 'season', 'city', 'tags', 'models', 'talent', 'language', 'country'));
        $apiResponse = $this->apiProvider->request('GET', '/api/designers/filter', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $results = json_decode((string) $apiResponse->getBody(), true);
        }

        return $results;
    }

    /**
     * @param string|null $city
     * @param string|null $season
     * @param string|null $individuals
     * @param string|null $tags
     * @param string|null $language
     *
     * @return Designer[]
     */
    public function listFiltersStreet(
        ?string $city,
        ?string $season,
        ?string $individuals,
        ?string $tags,
        ?string $language = null
    ): array {
        $results = [];
        $query = array_filter(compact('city', 'season', 'individuals', 'tags', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/designers/filter-streetstyle', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, Designer::class);
            }
        }

        return $results;
    }
}
