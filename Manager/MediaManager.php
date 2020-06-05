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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class MediaManager
{
    /** @var int default list size */
    public const DEFAULT_SIZE = 24;

    /** @var string default list medias sort for a model */
    public const DEFAULT_MEDIAS_MODEL_SORT = 'created_at:desc';

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
     * @var SerializerInterface
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
     * @return array|mixed
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
            $results = json_decode($apiResponse->getBody(), true);
        }

        return $results;
    }

    /**
     * @param array       $query
     * @param int         $from
     * @param int         $size
     * @param string      $status
     * @param string|null $language
     *
     * @return Media[]
     */
    public function list(
        $query = [],
        $from = 0,
        $size = self::DEFAULT_SIZE,
        $status = Status::ENABLED,
        $language = null
    ): array {
        $data = [];
        $query = array_merge($query, compact('from', 'size', 'status', 'language'));
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
        $query = array_merge($query, ['sort' => self::DEFAULT_MEDIAS_MODEL_SORT]);
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
}
