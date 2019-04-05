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
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\StreetstyleNormalizer;

class StreetstyleManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var StreetstyleNormalizer
     */
    private $streetstyleNormalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param StreetstyleNormalizer $streetstyleNormalizer
     */
    public function __construct(ApiProvider $apiProvider, StreetstyleNormalizer $streetstyleNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->streetstyleNormalizer = $streetstyleNormalizer;
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
     * @return null|Streetstyle
     */
    public function get(string $slug): ?Streetstyle
    {
        $media = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $media = $this->streetstyleNormalizer->denormalize($data, Streetstyle::class);
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } else {
            $this->logger->error($apiResponse->getBody()->getContents());
        }

        return $media;
    }
}
