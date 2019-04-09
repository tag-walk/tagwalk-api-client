<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagwalk\ApiClientBundle\Model\Media;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\MediaNormalizer;

class MediaManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var MediaNormalizer
     */
    private $mediaNormalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param MediaNormalizer $mediaNormalizer
     */
    public function __construct(ApiProvider $apiProvider, MediaNormalizer $mediaNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->mediaNormalizer = $mediaNormalizer;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param string $slug
     * @return null|Media
     */
    public function get(string $slug): ?Media
    {
        $media = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/medias/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $media = $this->mediaNormalizer->denormalize($data, Media::class);
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } else {
            $this->logger->error($apiResponse->getBody()->getContents());
        }

        return $media;
    }

    /**
     * @param string $type
     * @param string $season
     * @param string $designer
     * @param string $look
     * @return null|Media
     */
    public function findByTypeSeasonDesignerLook(string $type, string $season, string $designer, string $look): ?Media
    {
        $media = null;
        if ($type && $season && $designer && $look) {
            $apiResponse = $this->apiProvider->request(
                'GET',
                sprintf('/api/medias/%s/%s/%s/%s', $type, $season, $designer, $look),
                [RequestOptions::HTTP_ERRORS => false]
            );
            if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
                $data = json_decode($apiResponse->getBody(), true);
                $media = $this->mediaNormalizer->denormalize($data, Media::class);
            } elseif ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
                throw new NotFoundHttpException();
            } else {
                $this->logger->error($apiResponse->getBody()->getContents());
            }
        }

        return $media;
    }
}
