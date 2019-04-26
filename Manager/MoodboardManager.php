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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagwalk\ApiClientBundle\Model\Moodboard;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\MoodboardNormalizer;

class MoodboardManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var MoodboardNormalizer
     */
    private $moodboardNormalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     * @param MoodboardNormalizer $moodboardNormalizer
     */
    public function __construct(ApiProvider $apiProvider, MoodboardNormalizer $moodboardNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->moodboardNormalizer = $moodboardNormalizer;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger)
    {
        $this->logger = $logger;
    }

    /**
     * @param array $params
     * @return array
     */
    public function list(array $params): array
    {
        $list = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/moodboards/list-with-cover', [RequestOptions::QUERY => $params, RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $datum) {
                    $list[] = $this->moodboardNormalizer->denormalize($datum, Moodboard::class);
                }
            }
        }

        return $list;
    }

    /**
     * @param array $params
     * @return int
     */
    public function count(array $params): int
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/moodboards/list-with-cover', [RequestOptions::QUERY => array_merge($params, ['analytics' => 0]), RequestOptions::HTTP_ERRORS => false]);

        return (int)$apiResponse->getHeaderLine('X-Total-Count');
    }

    /**
     * @param string $slug
     * @return null|Moodboard
     */
    public function get(string $slug): ?Moodboard
    {
        $moodboard = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/moodboards/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $moodboard = $this->moodboardNormalizer->denormalize($data, Moodboard::class);
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } else {
            $this->logger->error($apiResponse->getBody()->getContents());
        }

        return $moodboard;
    }

    /**
     * @param string $token
     * @return null|Moodboard
     */
    public function getByToken(string $token): ?Moodboard
    {
        $moodboard = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/moodboards/shared/' . $token, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $moodboard = $this->moodboardNormalizer->denormalize($data, Moodboard::class);
        } else {
            $this->logger->error($apiResponse->getBody()->getContents());
        }

        return $moodboard;
    }

    /**
     * @param Moodboard $moodboard
     * @return Moodboard
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function create(Moodboard $moodboard): Moodboard
    {
        $params = [RequestOptions::JSON => $this->moodboardNormalizer->normalize($moodboard, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('POST', '/api/moodboards', $params);
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $data = json_decode($apiResponse->getBody(), true);
        } else {
            $this->logger->error($apiResponse->getBody()->getContents());
            throw new BadRequestHttpException();
        }
        $moodboard = $this->moodboardNormalizer->denormalize($data, Moodboard::class);

        return $moodboard;
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', '/api/moodboards/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_FORBIDDEN) {
            throw new AccessDeniedHttpException();
        }

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    /**
     * @param string $slug
     * @param string $type
     * @param string $lookSlug
     * @return bool
     */
    public function removeLook(string $slug, string $type, string $lookSlug): bool
    {
        $apiResponse = $this->apiProvider->request(
            'DELETE',
            sprintf('/api/moodboards/%s/%s/%s', $slug, $type === 'media' ? 'medias' : 'streetstyles', $lookSlug),
            [RequestOptions::HTTP_ERRORS => false]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_FORBIDDEN) {
            throw new AccessDeniedHttpException();
        }

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    /**
     * @param string $slug
     * @param Moodboard $moodboard
     * @return Moodboard
     */
    public function update(string $slug, Moodboard $moodboard): Moodboard
    {
        $params = [RequestOptions::JSON => $this->moodboardNormalizer->normalize($moodboard, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('PUT', '/api/moodboards/' . $slug, array_merge($params, [RequestOptions::HTTP_ERRORS => false]));
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $moodboard = $this->moodboardNormalizer->denormalize($data, Moodboard::class);
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new NotFoundHttpException();
        } else {
            $this->logger->error($apiResponse->getBody()->getContents());
            throw new BadRequestHttpException();
        }

        return $moodboard;
    }

    /**
     * @param string $slug
     * @param string $type
     * @param string $lookSlug
     * @return bool
     */
    public function addLook(string $slug, string $type, string $lookSlug): bool
    {
        $apiResponse = $this->apiProvider->request(
            'PUT',
            sprintf('/api/moodboards/%s/%s/%s', $slug, $type === 'media' ? 'medias' : 'streetstyles', $lookSlug),
            [RequestOptions::HTTP_ERRORS => false]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_FORBIDDEN) {
            throw new AccessDeniedHttpException();
        }

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }
}
