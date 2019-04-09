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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagwalk\ApiClientBundle\Model\Gallery;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\GalleryNormalizer;

class GalleryManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var GalleryNormalizer
     */
    private $galleryNormalizer;

    /**
     * @param ApiProvider $apiProvider
     * @param GalleryNormalizer $galleryNormalizer
     */
    public function __construct(ApiProvider $apiProvider, GalleryNormalizer $galleryNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->galleryNormalizer = $galleryNormalizer;
    }

    /**
     * @param string $slug
     * @return null|Gallery
     */
    public function get(string $slug): ?Gallery
    {
        $gallery = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/galleries/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $gallery = $this->galleryNormalizer->denormalize($data, Gallery::class);
        }

        return $gallery;
    }
}
