<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\NewsletterInsight;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class NewsletterInsightManager
{
    private const DEFAULT_SIZE = 10;
    private const DEFAULT_SORT = 'sent_at:desc';

    public int $lastCount;

    private ApiProvider $apiProvider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @return NewsletterInsight[]
     */
    public function list(
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $status = null,
        string $sort = self::DEFAULT_SORT
    ): array {
        $query = array_filter(compact('sort', 'from', 'size', 'status'));
        $apiResponse = $this->apiProvider->request('GET', '/api/newsletter/insights', [
            RequestOptions::QUERY => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        $data = [];
        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return $data;
        }

        $data = json_decode($apiResponse->getBody(), true);
        foreach ($data as $i => $datum) {
            $data[$i] = $this->serializer->denormalize($datum, NewsletterInsight::class);
        }

        $this->lastCount = $apiResponse->getHeaderLine('X-Total-Count');

        return $data;
    }
}
