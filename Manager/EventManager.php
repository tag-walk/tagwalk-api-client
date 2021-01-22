<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Event;
use Tagwalk\ApiClientBundle\Model\Poll;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class EventManager
{
    public const DEFAULT_SIZE = 24;
    public const DEFAULT_SORT = 'created_at:desc';

    public int $lastCount = 0;

    private ApiProvider $apiProvider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function get(int $id, ?string $language = null): ?Poll
    {

    }

    public function list(
        array $params = [],
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $language = null
    ): array {

    }

    public function create(Event $event): Event
    {
        $normalized = $this->serializer->normalize($event, null, ['write' => true]);
        $response = null;
        $apiResponse = $this->apiProvider->request('POST', 'api/event', [
            RequestOptions::HTTP_ERRORS => true,
            RequestOptions::JSON => $normalized
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            /** @var Event $response */
            $response = $this->serializer->deserialize((string) $apiResponse->getBody(), Event::class, JsonEncoder::FORMAT);
        }

        return $response;
    }
}
