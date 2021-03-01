<?php

namespace Tagwalk\ApiClientBundle\Event;

use Psr\Http\Message\RequestInterface;
use Psr\Http\Message\ResponseInterface;

class ResponseResolved
{
    const EVENT_NAME = 'TagwalkApiResponseResolved';

    private RequestInterface  $request;
    private ResponseInterface $response;
    private array $options;
    private float $requestedAt;
    private float $resolvedAt;

    public function __construct(
        RequestInterface  $request,
        ResponseInterface $response,
        array $options,
        float $requestedAt,
        float $resolvedAt
    ) {
        $this->request  = $request;
        $this->response = $response;
        $this->options  = $options;
        $this->requestedAt = $requestedAt;
        $this->resolvedAt  = $resolvedAt;
    }

    public function getRequest(): RequestInterface
    {
        return $this->request;
    }

    public function getResponse(): ResponseInterface
    {
        return $this->response;
    }

    public function getOptions(): array
    {
        return $this->options;
    }

    public function took(): float
    {
        return $this->resolvedAt - $this->requestedAt;
    }
}
