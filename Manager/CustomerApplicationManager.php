<?php

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class CustomerApplicationManager
{
    private ApiProvider $provider;

    public function __construct(ApiProvider $apiProvider)
    {
        $this->provider = $apiProvider;
    }

    public function list(): array
    {
        $response = $this->provider->request(Request::METHOD_GET, '/api/admin/customer-applications', [
            RequestOptions::HTTP_ERRORS => false
        ]);

        if ($response->getStatusCode() !== Response::HTTP_OK) {
            return [];
        }

        return json_decode($response->getBody(), true);
    }
}
