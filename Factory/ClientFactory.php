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

    private array $subscribers = [];

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
        AdapterInterface $cacheAdapter
    ) {
        $this->baseUri = $baseUri;
        $this->timeout = $timeout;
        $this->httpCache = $httpCache;
        $this->cacheAdapter = $cacheAdapter;
    }

    public function addSubscriber(callable $subscriber): self
    {
        $this->subscribers[] = $subscriber;
        return $this;
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

                    $promise = $handler($request, $options);

                    if ($promise instanceof \GuzzleHttp\Promise\FulfilledPromise) {
                        $promise->then(function($response) use ($request, $options, $startedAt) {
                            foreach ($this->subscribers as $subscriber) {
                                $subscriber($request, $response, $options, $startedAt);
                            }
                        });
                    }

                    return $promise;
                };
            },
            'observable'
        );

        return $stack;
    }
}
