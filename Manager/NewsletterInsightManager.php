<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
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

    public function get(string $slug): ?NewsletterInsight
    {
        $apiResponse = $this->apiProvider->request('GET', '/api/newsletter/insights/' . $slug, [
            RequestOptions::HTTP_ERRORS => false
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        $data = json_decode($apiResponse->getBody(), true);
        /** @var NewsletterInsight $data */
        $data = $this->serializer->denormalize($data, NewsletterInsight::class);

        return $data;
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

    public function create(NewsletterInsight $newsletter): ?NewsletterInsight
    {
        $apiResponse = $this->apiProvider->request('POST', '/api/newsletter/insights', [
            RequestOptions::JSON => $this->serializer->normalize($newsletter, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_CREATED) {
            return null;
        }

        $json = json_decode($apiResponse->getBody(), true);
        /** @var NewsletterInsight $data */
        $data = $this->serializer->denormalize($json, NewsletterInsight::class);

        return $data;
    }

    public function update(NewsletterInsight $newsletter): ?NewsletterInsight
    {
        $apiResponse = $this->apiProvider->request('PUT', '/api/newsletter/insights/' . $newsletter->getSlug(), [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => $this->serializer->normalize($newsletter, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var NewsletterInsight $updated */
        $updated = $this->serializer->deserialize(
            (string) $apiResponse->getBody(),
            NewsletterInsight::class,
            JsonEncoder::FORMAT
        );

        return $updated;
    }

    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', '/api/newsletter/insights/' . $slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY => ['refresh' => true]
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }
}
