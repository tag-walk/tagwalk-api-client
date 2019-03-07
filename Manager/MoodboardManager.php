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
use Symfony\Component\HttpKernel\Exception\AccessDeniedHttpException;
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
     * @param ApiProvider $apiProvider
     * @param MoodboardNormalizer $moodboardNormalizer
     */
    public function __construct(ApiProvider $apiProvider, MoodboardNormalizer $moodboardNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->moodboardNormalizer = $moodboardNormalizer;
    }

    /**
     * @param array $params
     * @return array
     */
    public function list(array $params): array
    {
        $list = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/moodboards/list-with-cover', ['query' => $params, RequestOptions::HTTP_ERRORS => false]);
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
     * @param string $emmail
     * @return int
     */
    public function countList(string $email): int
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/moodboards/list-with-cover', ['query' => ['email' => $email], [RequestOptions::HTTP_ERRORS => false]]);
        $count = $apiResponse->getHeader('X-Total-Count');

        return isset($count[0]) ? $count[0] : 0;
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
        $data = json_decode($apiResponse->getBody(), true);
        $moodboard = $this->moodboardNormalizer->denormalize($data, Moodboard::class);

        return $moodboard;
    }

    /**
     * @param string $slug
     * @return bool
     */
    public function delete(string $slug)
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
    public function removeLook(string $slug, string $type, string $lookSlug)
    {
        $apiResponse = null;
        if ($type === 'media') {
            $apiResponse = $this->apiProvider->request('DELETE', '/api/moodboards/' . $slug . '/medias/' . $lookSlug, [RequestOptions::HTTP_ERRORS => false]);
        } else {
            $apiResponse = $this->apiProvider->request('DELETE', '/api/moodboards/' . $slug . '/streetstyles/' . $lookSlug, [RequestOptions::HTTP_ERRORS => false]);
        }
        if ($apiResponse->getStatusCode() === Response::HTTP_FORBIDDEN) {
            throw new AccessDeniedHttpException();
        }

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }
}
