<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class MediaManager
{
    /** @var int default list size */
    public const DEFAULT_SIZE = 24;

    /** @var string default list medias sort for a model */
    public const DEFAULT_CREATION_SORT = 'created_at:desc';

    public const DEFAULT_LIST_SORT = 'season.position:asc,designer.name.keyword:asc,city.name.keyword:asc,type:desc,look.number:asc';

    /**
     * @var int last query result count
     */
    public $lastCount;

    /**
     * @var int news result count of an individual
     */
    public $individualNewsCount;

    /**
     * @var int streetstyles result count of an individual
     */
    public $individualStreetstylesCount;

    /**
     * @var int talks result count of an individual
     */
    public $individualTalksCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function create(Media $media): ?Media
    {
        $data = $this->serializer->normalize($media, null, ['write' => true]);
        $apiResponse = $this->apiProvider->request('POST', '/api/medias', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $data,
        ]);
        $created = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Media $created */
            $created = $this->serializer->deserialize((string) $apiResponse->getBody(), Media::class, JsonEncoder::FORMAT);
        }

        return $created;
    }

    /**
     * @param string $slug
     *
     * @return null|Media
     */
    public function get(string $slug): ?Media
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/medias/'.$slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            /** @var Media $data */
            $data = $this->serializer->denormalize($data, Media::class);
        }

        return $data;
    }

    /**
     * @param string $type
     * @param string $season
     * @param string $designer
     * @param string $look
     *
     * @return null|Media
     */
    public function findByTypeSeasonDesignerLook(string $type, string $season, string $designer, string $look): ?Media
    {
        $media = null;
        $apiResponse = $this->apiProvider->request(
            'GET',
            sprintf('/api/medias/%s/%s/%s/%s', $type, $season, $designer, $look),
            [RequestOptions::HTTP_ERRORS => false]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $media = $this->serializer->denormalize($data, Media::class);
        }

        return $media;
    }

    /**
     * @param string      $type
     * @param string      $season
     * @param string      $designer
     * @param string|null $city
     *
     * @return array
     */
    public function listRelated(string $type, string $season, string $designer, ?string $city = null): array
    {
        $results = [];
        $query = array_merge([
            'analytics' => 0,
            'from'      => 0,
            'size'      => 6,
        ], compact('type', 'season', 'designer', 'city'));
        $apiResponse = $this->apiProvider->request('GET', '/api/medias', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $results = json_decode($apiResponse->getBody(), true, 512, JSON_THROW_ON_ERROR);
        }

        return $results;
    }

    /**
     * @param array       $query
     * @param int         $from
     * @param int         $size
     * @param string      $status
     * @param string|null $language
     * @param string      $sort
     *
     * @return Media[]
     */
    public function list(
        $query = [],
        $from = 0,
        $size = self::DEFAULT_SIZE,
        $status = Status::ENABLED,
        $language = null,
        $sort = self::DEFAULT_LIST_SORT
    ): array {
        $data = [];
        $query = array_merge($query, compact('from', 'size', 'status', 'language', 'sort'));
        $apiResponse = $this->apiProvider->request('GET', '/api/medias', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            foreach ($data as $i => $datum) {
                $data[$i] = $this->serializer->denormalize($datum, Media::class);
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $data;
    }

    /**
     * Find medias looks by model slug.
     *
     * @param string $slug
     * @param array  $query
     *
     * @return Media[]
     */
    public function listByModel(string $slug, array $query = []): array
    {
        $query = array_merge($query, ['sort' => self::DEFAULT_CREATION_SORT]);
        $data = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/'.$slug.'/medias', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $medias = json_decode($apiResponse->getBody(), true);
            if (!empty($medias)) {
                foreach ($medias as $media) {
                    $data[] = $this->serializer->denormalize($media, Media::class);
                }
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $this->individualStreetstylesCount = (int) $apiResponse->getHeaderLine('X-Streetstyles-Count');
            $this->individualNewsCount = (int) $apiResponse->getHeaderLine('X-News-Count');
            $this->individualTalksCount = (int) $apiResponse->getHeaderLine('X-Talks-Count');
        }

        return $data;
    }

    /**
     * @param Media $media
     *
     * @return Media|null
     */
    public function update(Media $media): ?Media
    {
        $updated = null;
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PUT,
            sprintf('/api/medias/%s', $media->getSlug()),
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::JSON        =>  $this->serializer->normalize($media, null, ['write' => true]),
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $updated = $this->serializer->deserialize((string) $apiResponse->getBody(), Media::class, JsonEncoder::FORMAT);
        }

        return $updated;
    }

    public function suggestWatermarks(string $prefix): array
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/medias/watermarks/suggestions', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => compact('prefix')
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return [];
        }

        return json_decode($apiResponse->getBody());
    }

    public function addTags(array $tags, array $mediaSlugs): bool
    {
        $query = [
            'tags' => implode(',', $tags),
            'slugs' => implode(',', $mediaSlugs)
        ];

        $apiResponse = $this->apiProvider->request(Request::METHOD_POST, '/api/medias/tags', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => $query
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function removeTags(array $slugs, array $tags): bool
    {
        $query = [
            'slugs' => implode(',', $slugs),
            'tags'  => implode(',', $tags)
        ];

        $apiReponse = $this->apiProvider->request(
            Request::METHOD_POST,
            '/api/medias/tags/remove',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => $query,
            ]
        );

        return $apiReponse->getStatusCode() === Response::HTTP_OK;
    }

    public function listTagsByMediaSlugs(array $slugs): array
    {
        $tags = [];
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/medias/tags/list',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => ['slugs' => implode(',', $slugs)]
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $tags = json_decode($apiResponse->getBody(), true);
        }

        return $tags;
    }

    public function addType(string $type, array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/type',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'type'  => $type,
                    'slugs' => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function addSeason(string $season, array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/season',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'season' => $season,
                    'slugs'  => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function addDesigner(string $designer, array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/designer',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'designer' => $designer,
                    'slugs'    => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function addWatermark(string $watermark, array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/watermark',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'watermark' => $watermark,
                    'slugs'     => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function removeWatermarks(array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/watermark/remove',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'slugs' => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function addCourtesy(string $courtesy, array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/courtesy',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'courtesy' => $courtesy,
                    'slugs'    => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function addLabel(string $label, array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_POST, '/api/medias/label',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => [
                    'label' => $label,
                    'slugs' => implode(',', $slugs),
                ]
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function toggleStatuses(array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PATCH, '/api/medias/status', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => ['slugs' => implode(',', $slugs)]
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function delete(Media $media): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_DELETE, '/api/medias/' . $media->getSlug(), [
            RequestOptions::HTTP_ERRORS => false
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function deleteMultiple(array $slugs): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_DELETE, '/api/medias', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => ['slugs' => implode(',', $slugs)]
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function reorder(array $items): int
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PUT, '/api/medias/reorder',
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::BODY        => json_encode($items),
                RequestOptions::HEADERS     => [
                    'Content-type' => 'application/json',
                ],
            ]
        );

        return $apiResponse->getStatusCode();
    }

    public function getLastPosition(array $params): ?int
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/medias/last-position',
            [
                RequestOptions::QUERY       => $params,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }
        $result = json_decode($apiResponse->getBody(), true);

        return $result['position'] ?? null;
    }
 }
