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
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\HomepageNormalizer;

class HomepageManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var HomepageNormalizer
     */
    private $homepageNormalizer;

    /**
     * @param ApiProvider $apiProvider
     * @param HomepageNormalizer $homepageNormalizer
     */
    public function __construct(ApiProvider $apiProvider, HomepageNormalizer $homepageNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->homepageNormalizer = $homepageNormalizer;
    }

    /**
     * @param string $section
     * @return null|Homepage
     */
    public function get(string $section): ?Homepage
    {
        $homepage = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/homepages/show/' . $section, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $homepage = $this->homepageNormalizer->denormalize($data, Homepage::class);
        }

        return $homepage;
    }
}
