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
}
