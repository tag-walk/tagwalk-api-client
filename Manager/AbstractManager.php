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
    protected string $createEndpoint;
    protected string $updateEndpoint;
    protected string $deleteEndpoint;
    protected string $modelClass;
    public int $lastCount = 0;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->provider = $apiProvider;
        $this->serializer = $serializer;

        $this->getEndpoint = $this->getGetEndpoint();
        $this->createEndpoint = $this->getCreateEndpoint();
        $this->updateEndpoint = $this->getUpdateEndpoint();
        $this->listEndpoint = $this->getListEndpoint();
        $this->deleteEndpoint = $this->getDeleteEndpoint();
        $this->modelClass = $this->getModelClass();
    }

    abstract protected function getGetEndpoint(): string;
    abstract protected function getCreateEndpoint(): string;
    abstract protected function getUpdateEndpoint(): string;
    abstract protected function getListEndpoint(): string;
    abstract protected function getDeleteEndpoint(): string;
    abstract protected function getModelClass(): string;

    public function create(object $document)
    {
        $normalized = $this->serializer->normalize($document, null, ['write' => true]);

        $response = $this->provider->request(
            Request::METHOD_POST,
            $this->createEndpoint,
            [
                RequestOptions::JSON => $normalized,
                RequestOptions::HTTP_ERRORS => true
            ]
        );

        if ($response->getStatusCode() !== Response::HTTP_CREATED) {
            return null;
        }

        return $this->serializer->deserialize(
            $response->getBody(),
            $this->modelClass,
            JsonEncoder::FORMAT
        );
    }

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

    public function delete(string $slug): bool
    {
        $response = $this->provider->request(
            Request::METHOD_DELETE,
            $this->deleteEndpoint . $slug,
            [RequestOptions::HTTP_ERRORS => true]
        );

        return $response->getStatusCode() === Response::HTTP_NO_CONTENT;
    }
}
