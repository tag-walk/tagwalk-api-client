<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Poll;
use Tagwalk\ApiClientBundle\Model\PollAnswer;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class PollManager
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

    public function addVote(PollAnswer $answer): ?Poll
    {
        $apiResponse = $this->apiProvider->request('POST', '/api/poll/vote', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => $this->serializer->normalize($answer, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        $data = json_decode($apiResponse->getBody());
        /** @var Poll $data */
        $data = $this->serializer->denormalize($data, Poll::class);

        return $data;
    }

    public function get(int $id, ?string $language = null): ?Poll
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/poll/' . $id, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => array_filter(['language' => $language]),
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var Poll $data */
        $data = $this->serializer->deserialize($apiResponse->getBody(), Poll::class, JsonEncoder::FORMAT);

        return $data;
    }

    /**
     * @return Poll[]
     */
    public function list(
        array $params = [],
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $language = null
    ): array {
        $params = array_merge(
            $params,
            array_filter(compact('sort', 'from', 'size', 'language'))
        );

        $apiResponse = $this->apiProvider->request('GET', '/api/poll', [
            RequestOptions::QUERY => $params,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return [];
        }

        $polls = json_decode($apiResponse->getBody(), true);
        foreach ($polls as $key => $poll) {
            $polls[$key] = $this->serializer->denormalize($poll, Poll::class);
        }

        $this->lastCount = $apiResponse->getHeaderLine('X-Total-Count');

        return $polls;
    }
}
