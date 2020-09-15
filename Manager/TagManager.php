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
     * @var int
     */
    public $lastCount;

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
            $tag = $this->serializer->deserialize((string) $apiResponse->getBody(), Tag::class, JsonEncoder::FORMAT);
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
            $data = json_decode((string) $apiResponse->getBody(), true);
            if ($denormalize) {
                foreach ($data as $datum) {
                    $tags[] = $this->serializer->denormalize($datum, Tag::class);
                }
            } else {
                $tags = $data;
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $tags;
    }

    /**
     * @param string $status
     *
     * @return int|null
     */
    public function count(string $status = self::DEFAULT_STATUS): ?int
    {
        $count = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/tags', [
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
        $tags = [];
        $query = array_filter(compact('prefix', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/tags/suggestions', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $tags = json_decode((string) $apiResponse->getBody(), true);
        }

        return $tags;
    }

    /**
     * $query['tags']        = (string) A comma-seperated string of tags selected to restrict the result (require true)
     * $query['type']        = (string) The type selected to restrict the result (optional)
     * $query['city']        = (string) The city selected to restrict the result (optional)
     * $query['season']      = (string) The season selected to restrict the result (optional)
     * $query['designer']    = (string) The designer selected to restrict the result (optional)
     * $query['individuals'] = (string) The individual selected to restrict the result (optional)
     * $query['streetstyle'] = (bool) To find in index streetstyles or medias (require true)
     * $query['language']    = (optional)
     *
     * @param array $query
     *
     * @return array
     */
    public function similars(array $query): array
    {
        $tagsSimilars = [];
        $apiResponse = $this->apiProvider->request(
            'GET',
            '/api/tags/similars',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => $query,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $tagsSimilars = json_decode((string) $apiResponse->getBody(), true);
        }

        return $tagsSimilars;
    }

    /**
     * @param string      $search
     * @param null|string $language
     *
     * @return array
     */
    public function nearby(string $search, ?string $language = null): array
    {
        $tags = [];
        $query = array_filter(compact('search', 'language'));
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/tags/nearby',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => $query,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $tags = json_decode((string) $apiResponse->getBody(), true);
        }

        return $tags;
    }
}
