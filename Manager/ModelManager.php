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
use Tagwalk\ApiClientBundle\Model\Individual;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ModelManager extends IndividualManager
{
    /**
     * @var int last list count
     */
    public $lastCount;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer  $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        Serializer $serializer
    ) {
        parent::__construct($apiProvider, $serializer);
    }

    /**
     * @param int $size
     *
     * @return array
     */
    public function modelsTrends(int $size = 10): array
    {
        $data = [];
        $this->lastCount = 0;
        $query = ['size' => $size];
        $apiResponse = $this->apiProvider->request('GET', '/api/models/trends', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $contents = json_decode($apiResponse->getBody()->getContents(), true);
            $newfaces = json_decode($contents['newfaces']['content'], true);
            $womanData = json_decode($contents['womenswear']['content'], true);
            $manData = json_decode($contents['menswear']['content'], true);
            $data = [
                'newFaces'      => $newfaces,
                'womanData'     => $womanData,
                'manData'       => $manData,
                'globalWoman'   => $womanData['global'] ?? null,
                'globalMan'     => $manData['global'] ?? null,
                'season'        => $contents['season'],
                'countNewFaces' => $this->lastCount,
            ];
            unset($womanData['global']);
            $data = array_merge($data, ['cities' => $womanData]);
        } else {
            $this->logger->error('ModelManager::index unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param string $type
     * @param string $season
     * @param string $city
     * @param int    $length
     *
     * @return array
     */
    public function whoWalkedTheMost($type = null, $season = null, $city = null, $length = 10): array
    {
        $data = [];
        $this->lastCount = 0;
        $query = array_filter(compact('type', 'season', 'city', 'length'));
        $apiResponse = $this->apiProvider->request('GET', '/api/models/who-walked-the-most', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $i => $datum) {
                    $data[$i] = $this->serializer->denormalize($datum, Individual::class);
                }
            }
        } else {
            $this->logger->error('ModelManager::whoWalkedTheMost unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param int   $size
     * @param int   $page
     * @param array $query
     *
     * @return array
     */
    public function listMediasModels(int $size, int $page, array $query = []): array
    {
        $data = [];
        $this->lastCount = 0;
        $query = array_merge($query, [
            'size' => $size,
            'page' => $page,
        ]);
        $apiResponse = $this->apiProvider->request('GET', '/api/models', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $i => $datum) {
                    $data[$i] = $this->serializer->denormalize($datum, Individual::class);
                }
            }
        } else {
            $this->logger->error('ModelManager::listMediasModels unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @return Individual[]
     */
    public function getNewFaces(): array
    {
        $data = [];
        $this->lastCount = 0;
        $apiResponse = $this->apiProvider->request('GET', '/api/models/new-faces', [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $i => $datum) {
                    $data[$i] = $this->serializer->denormalize($datum, Individual::class);
                }
            }
        } else {
            $this->logger->error('ModelManager::getNewFaces unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param null|string $type
     * @param null|string $season
     * @param null|string $city
     * @param string|null $language
     *
     * @return array representation of individuals
     */
    public function listFilters(
        ?string $type,
        ?string $season,
        ?string $city,
        ?string $language = null
    ): array {
        $models = [];
        $query = array_filter(compact('type', 'season', 'city', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/models/filter', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $models = json_decode($apiResponse->getBody()->getContents(), true);
        } else {
            $this->logger->error('ModelManager::listFilters unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $models;
    }

    /**
     * @param int         $size
     * @param string|null $type
     *
     * @return array
     */
    public function listTop(?int $size = 10, ?string $type = null): array
    {
        $data = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/models/top', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => array_filter(compact('size', 'type')),
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
        } else {
            $this->logger->error('ModelManager::listTop error', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }
}
