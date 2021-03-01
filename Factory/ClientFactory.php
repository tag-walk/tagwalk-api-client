<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Factory;

use GuzzleHttp\Client;
use GuzzleHttp\HandlerStack;
use Kevinrob\GuzzleCache\CacheMiddleware;
use Kevinrob\GuzzleCache\Storage\Psr6CacheStorage;
use Kevinrob\GuzzleCache\Strategy\PrivateCacheStrategy;
use Symfony\Component\Cache\Adapter\AdapterInterface;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Tagwalk\ApiClientBundle\Event\ResponseResolved;

class ClientFactory
{
    /** @var float api request default timeout */
    private const DEFAULT_TIMEOUT = 30.0;

    /**
     * @var Client
     */
    private $client;

    /**
     * @var string
     */
    private $baseUri;

    /**
     * @var float
     */
    private $timeout;

    /**
     * @var bool
     */
    private $httpCache;

    /**
     * @var AdapterInterface
     */
    private $cacheAdapter;

    /**
     * @var EventDispatcherInterface
     */
    private $dispatcher;

    /**
     * @param string        $baseUri
     * @param float             $timeout
     * @param bool              $httpCache
     * @param AbstractAdapter   $cacheAdapter
     */
    public function __construct(
        string $baseUri = '',
        float $timeout = self::DEFAULT_TIMEOUT,
        bool $httpCache = true,
        AdapterInterface $cacheAdapter,
        EventDispatcherInterface $dispatcher
    ) {
        $this->baseUri = $baseUri;
        $this->timeout = $timeout;
        $this->httpCache = $httpCache;
        $this->cacheAdapter = $cacheAdapter;
        $this->dispatcher = $dispatcher;
    }

    /**
     * Lazyloading client instance
     *
     * @return Client
     */
    public function get(): Client
    {
        return $this->client ?? $this->create();
    }

    /**
     * Create a new instance of client and init class param
     *
     * @return Client
     */
    private function create(): Client
    {
        $params = [
            'base_uri' => $this->baseUri,
            'timeout'  => $this->timeout,
            'handler'  => $this->getClientHandlerStack(),
        ];

        $this->client = new Client($params);

        return $this->client;
    }

    /**
     * Prepare the client handler stack
     *
     * @return HandlerStack
     */
    private function getClientHandlerStack(): HandlerStack
    {
        $stack = HandlerStack::create();

        if ($this->httpCache) {
            $stack->push(
                new CacheMiddleware(
                    new PrivateCacheStrategy(
                        new Psr6CacheStorage($this->cacheAdapter)
                    )
                ),
                'cache'
            );
        }

        $stack->push(
            function($handler) {
                return function($request, $options) use ($handler) {
                    $startedAt = microtime(true);

                    $response = $handler($request, $options);

                    if ($response instanceof \GuzzleHttp\Promise\FulfilledPromise) {
                        $response->then(function($response) use ($request, $options, $startedAt) {
                            $profiler = [
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

                            // var_dump($profiler);

                            $this->dispatcher->dispatch(
                                new ResponseResolved($request, $response, $options, $startedAt, microtime(true)),
                                ResponseResolved::EVENT_NAME
                            );
                        });
                    }

                    return $response;
                };
            },
            'observable'
        );

        return $stack;
    }
}
