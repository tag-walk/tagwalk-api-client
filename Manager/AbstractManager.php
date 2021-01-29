<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

abstract class AbstractManager
{
    private ApiProvider $provider;
    private SerializerInterface $serializer;
    protected string $listEndpoint;
    protected string $getEndpoint;
    protected string $updateEndpoint;
    protected string $modelClass;
    public int $lastCount = 0;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->provider = $apiProvider;
        $this->serializer = $serializer;

        $this->setGetEndpoint();
        $this->setUpdateEndpoint();
        $this->setListEndpoint();
        $this->setModelClass();
    }

    abstract protected function setGetEndpoint();
    abstract protected function setUpdateEndpoint();
    abstract protected function setListEndpoint();
    abstract protected function setModelClass();

    public function update(string $slug, object $document)
    {
        $normalized = $this->serializer->normalize($document, null, ['write' => true]);

        $response = $this->provider->request(
            Request::METHOD_PUT,
            $this->updateEndpoint . $slug,
            [
                RequestOptions::JSON => $normalized,
                RequestOptions::HTTP_ERRORS => true
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return $this->serializer->deserialize(
            $response->getBody(),
            $this->modelClass,
            JsonEncoder::FORMAT
        );
    }

    public function get(string $slug): object
    {
        $response = $this->provider->request(
            Request::METHOD_GET,
            $this->getEndpoint . $slug,
            [
                RequestOptions::HTTP_ERRORS => true
            ]
        );

        return $this->serializer->deserialize(
            $response->getBody(),
            $this->modelClass,
            JsonEncoder::FORMAT
        );
    }

    public function list(array $query = []): array
    {
        $response = $this->provider->request(
            Request::METHOD_GET,
            $this->listEndpoint,
            [
                'query' => $query,
                RequestOptions::HTTP_ERRORS => true
            ]
        );

        $this->lastCount = (int) $response->getHeaderLine('X-Total-Count');

        return $this->serializer->deserialize(
            $response->getBody(),
            $this->modelClass . '[]',
            JsonEncoder::FORMAT
        );
    }
}
