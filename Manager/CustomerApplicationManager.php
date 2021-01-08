<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;

use Tagwalk\ApiClientBundle\Model\CustomerApplication;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CustomerApplicationManager
{
    private ApiProvider $provider;
    private SerializerInterface $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->provider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function list(): array
    {
        $response = $this->provider->request(
            Request::METHOD_GET,
            '/api/admin/customer-applications',
            [ RequestOptions::HTTP_ERRORS => true ]
        );

        return $this->serializer->deserialize($response->getBody(), CustomerApplication::class . '[]', 'json');
    }
}
