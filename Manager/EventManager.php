<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Event;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class EventManager
{
    public const DEFAULT_SIZE = 48;
    public const DEFAULT_SORT = 'created_at:desc';

    public int $lastCount = 0;

    private ApiProvider $apiProvider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function get(string $slug): ?Event
    {
        $apiResponse = $this->apiProvider->request('GET', sprintf('api/event/%s', $slug), [
            RequestOptions::HTTP_ERRORS => true,
        ]);

        $response = null;

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Event $response */
            $response = $this->serializer->deserialize((string) $apiResponse->getBody(), Event::class, JsonEncoder::FORMAT);
        }

        return $response;
    }


    public function list(
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        array $params = []
    ): array {
        $query = array_merge($params, array_filter(compact('from', 'size', 'sort')));

        $apiResponse = $this->apiProvider->request('GET', 'api/event', [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::QUERY => $query
        ]);

        $response = [];

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $response = $this->serializer->deserialize($apiResponse->getBody(), Event::class . '[]', 'json');
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $response;
    }

    public function create(Event $event): ?Event
    {
        $normalized = $this->serializer->normalize($event, null, ['write' => true]);

        $apiResponse = $this->apiProvider->request('POST', 'api/event', [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::JSON => $normalized
        ]);

        $response = null;

        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            /** @var Event $response */
            $response = $this->serializer->deserialize((string) $apiResponse->getBody(), Event::class, JsonEncoder::FORMAT);
        }

        return $response;
    }

    public function update(Event $event): ?Event
    {
        $normalized = $this->serializer->normalize($event);

        $apiResponse = $this->apiProvider->request('PUT', sprintf('api/event/%s', $event->getSlug()), [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::JSON => $normalized
        ]);

        $response = null;

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Event $response */
            $response = $this->serializer->deserialize((string) $apiResponse->getBody(), Event::class, JsonEncoder::FORMAT);
        }

        return $response;
    }

    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', sprintf('api/event/%s', $slug), [
            RequestOptions::HTTP_ERRORS => true,
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }
}
