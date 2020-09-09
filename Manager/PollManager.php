<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Poll;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class PollManager
{
    private ApiProvider $apiProvider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function addVote(int $pollId, int $answerId): ?Poll
    {
        $path = sprintf('/api/poll/vote/%s/%s', $pollId, $answerId);
        $apiResponse = $this->apiProvider->request('POST', $path, [RequestOptions::HTTP_ERRORS => false]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        $data = json_decode($apiResponse->getBody(), true);
        /** @var Poll $data */
        $data = $this->serializer->denormalize($data, Poll::class);

        return $data;
    }
}
