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
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Model\Individual;

class ModelManager extends IndividualManager
{
    /**
     * @var int last list count
     */
    public $lastCount;

    /**
     * @param int $size
     *
     * @return array
     */
    public function modelsTrends(int $size = 10): array
    {
        $data = [];
        $query = ['size' => $size];
        $apiResponse = $this->apiProvider->request('GET', '/api/models/trends', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
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
        $this->lastCount = 0;
        $query = array_filter(compact('type', 'season', 'city', 'length'));
        $apiResponse = $this->apiProvider->request('GET', '/api/models/who-walked-the-most', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return $this->deserializeListResponse($apiResponse);
    }

    /**
     * @param ResponseInterface $apiResponse
     *
     * @return Individual[]
     */
    private function deserializeListResponse(ResponseInterface $apiResponse): array
    {
        $data = [];
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $i => $datum) {
                    $data[$i] = $this->serializer->denormalize($datum, Individual::class);
                }
            }
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
    public function listMediasModels(int $size, int $page = 1, array $query = []): array
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/models', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => array_merge($query, [
                'size' => $size,
                'page' => $page,
            ]),
        ]);

        return $this->deserializeListResponse($apiResponse);
    }

    /**
     * @return Individual[]
     */
    public function getNewFaces(): array
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/models/new-faces', [RequestOptions::HTTP_ERRORS => false]);

        return $this->deserializeListResponse($apiResponse);
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
            $models = json_decode((string) $apiResponse->getBody(), true);
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
        }

        return $data;
    }
}
