<?php

namespace Tagwalk\ApiClientBundle\DataCollector;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\DataCollector\DataCollector;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;

final class ApiDataCollector extends DataCollector
{
    private $stack = [];

    public function onResponse($request, $response, $options, $startedAt)
    {
        $this->stack[] = [
            'request'  => [
                'method' => $request->getMethod(),
                'uri' => (string) $request->getUri(),
            ],
            'response' => [
                'status' => $response->getStatusCode(),
                'Last-Modified' => ($response->getHeader('Last-Modified') ?? [null])[0],
                'X-Total-Count' => ($response->getHeader('X-Total-Count') ?? [null])[0],
                'X-Debug-Token-Link' => ($response->getHeader('X-Debug-Token-Link') ?? [null])[0],
                'took' => microtime(true) - $startedAt
            ],
        ];
    }

    public function collect(Request $request, Response $response, \Throwable $exception = null)
    {
        $this->reset();
        $this->data['stack'] = $this->stack;
        $this->stack = [];
    }

    public function getName(): string
    {
        return 'tagwalk_api_client';
    }

    public function getRequestCount(): int
    {
        return count($this->data['stack']);
    }

    public function tookMilliseconds(): int
    {
        $responses = array_column($this->data['stack'], 'response');
        $took = array_column($responses, 'took');
        return ceil(array_sum($took) * 1000);
    }

    public function countByStatusRange(int $from, int $to): int
    {
        $responses = array_column($this->data['stack'], 'response');
        $codes = array_column($responses, 'status');
        $codes = array_filter($codes, function($code) use ($from, $to) {
            return $code >= $from && $code <= $to;
        });

        return count($codes);
    }

    public function getRequestStack(): array
    {
        return $this->data['stack'];
    }

    public function reset()
    {
        $this->data = [
            'stack' => [],
        ];
    }
}
