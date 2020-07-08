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
use Psr\Http\Message\ResponseInterface;
use Psr\Http\Message\StreamInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Moodboard;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Provider\MoodboardProvider;

class MoodboardManager
{
    public const DEFAULT_SIZE = 12;

    /**
     * @var int
     */
    public $lastCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var MoodboardProvider
     */
    private $moodboardProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param ApiProvider          $apiProvider
     * @param MoodboardProvider    $moodboardProvider
     * @param SerializerInterface  $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        MoodboardProvider $moodboardProvider,
        SerializerInterface $serializer
    ) {
        $this->apiProvider = $apiProvider;
        $this->moodboardProvider = $moodboardProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param array $params
     *
     * @return array
     */
    public function list(array $params): array
    {
        $list = [];
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/moodboards/', [
            RequestOptions::QUERY       => $params,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            if (!empty($data)) {
                foreach ($data as $datum) {
                    $list[] = $this->serializer->denormalize($datum, Moodboard::class);
                }
                $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            }
        }

        return $list;
    }

    /**
     * @param array $params
     *
     * @return int
     */
    public function count(array $params): int
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/moodboards/', [
            RequestOptions::QUERY       => $params,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return (int) $apiResponse->getHeaderLine('X-Total-Count');
    }

    /**
     * @param string     $slug
     * @param array|null $params
     *
     * @return null|Moodboard
     */
    public function get(string $slug, $params = []): ?Moodboard
    {
        $moodboard = null;
        $data = $this->moodboardProvider->get($slug, $params);
        if ($data !== null) {
            /** @var Moodboard $moodboard */
            $moodboard = $this->serializer->denormalize($data, Moodboard::class);
            $this->lastCount = $this->moodboardProvider->lastCount;
        }

        return $moodboard;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return Moodboard
     */
    private function denormalizeResponse(ResponseInterface $response): Moodboard
    {
        $data = json_decode($response->getBody(), true);
        /** @var Moodboard $moodboard */
        $moodboard = $this->serializer->denormalize($data, Moodboard::class);

        return $moodboard;
    }

    /**
     * @param string $token
     *
     * @return null|Moodboard
     */
    public function getByToken(string $token): ?Moodboard
    {
        $moodboard = null;
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/moodboards/shared/'.$token, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $moodboard = $this->denormalizeResponse($apiResponse);
        }

        return $moodboard;
    }

    /**
     * @param string $token
     *
     * @return StreamInterface|null
     */
    public function getPdfByToken(string $token): ?StreamInterface
    {
        $pdf = null;
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/moodboards/pdf/'.$token, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $pdf = $apiResponse->getBody();
        }

        return $pdf;
    }

    /**
     * @param Moodboard $moodboard
     *
     * @return Moodboard
     */
    public function create(Moodboard $moodboard): ?Moodboard
    {
        $params = [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $this->serializer->normalize($moodboard, null, ['write' => true]),
        ];
        $response = null;
        $apiResponse = $this->apiProvider->request(Request::METHOD_POST, '/api/moodboards', $params);
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $response = $this->denormalizeResponse($apiResponse);
        }

        return $response;
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_DELETE, '/api/moodboards/'.$slug, [RequestOptions::HTTP_ERRORS => false]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    /**
     * @param string $slug
     * @param string $type
     * @param string $lookSlug
     *
     * @return bool
     */
    public function removeLook(string $slug, string $type, string $lookSlug): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_DELETE,
            sprintf(
                '/api/moodboards/%s/item/%s/%s',
                $slug,
                $type === 'media' ? 'medias' : 'streetstyles',
                $lookSlug
            ),
            [RequestOptions::HTTP_ERRORS => false]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    /**
     * @param string    $slug
     * @param Moodboard $moodboard
     *
     * @return Moodboard
     */
    public function update(string $slug, Moodboard $moodboard): ?Moodboard
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($moodboard, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PUT,
            '/api/moodboards/'.$slug,
            array_merge(
                $params,
                [RequestOptions::HTTP_ERRORS => false]
            )
        );
        $response = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $response = $this->denormalizeResponse($apiResponse);
        }

        return $response;
    }

    /**
     * @param string $slug
     * @param string $itemSlug
     * @param int $originalPosition
     * @param int $updatedPosition
     *
     * @return bool
     */
    public function reorder(string $slug, string $itemSlug, int $originalPosition, int $updatedPosition): bool
    {
        $item = [
            'slug' => $itemSlug,
            'originalPosition' => $originalPosition,
            'updatedPosition' => $updatedPosition
        ];
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PUT,
            sprintf('/api/moodboards/%s/reorder', $slug),
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::BODY        => json_encode($item),
                RequestOptions::HEADERS     => [
                    'Content-type' => 'application/json',
                ],
            ]
        );

        return $apiResponse->getStatusCode();
    }

    /**
     * @param string $slug
     * @param string $name
     *
     * @return bool
     */
    public function rename(string $slug, string $name): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PATCH,
            sprintf('/api/moodboards/%s/rename', $slug),
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => ['name' => $name],
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    /**
     * @param string $slug
     * @param string $type
     * @param string $lookSlug
     *
     * @return bool
     */
    public function addLook(string $slug, string $type, string $lookSlug): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PUT,
            sprintf(
                '/api/moodboards/%s/item/%s/%s',
                $slug,
                $type === 'media' ? 'medias' : 'streetstyles',
                $lookSlug
            ),
            [RequestOptions::HTTP_ERRORS => false]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }
}
