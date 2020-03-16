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
use Symfony\Component\Cache\Adapter\FilesystemAdapter;

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
     * @var string|null
     */
    private $cacheDirectory;

    /**
     * @param string      $baseUri
     * @param float       $timeout
     * @param bool        $httpCache
     * @param string|null $cacheDirectory
     */
    public function __construct(
        string $baseUri,
        float $timeout = self::DEFAULT_TIMEOUT,
        bool $httpCache = true,
        ?string $cacheDirectory = null
    ) {
        $this->baseUri = $baseUri;
        $this->timeout = $timeout;
        $this->httpCache = $httpCache;
        $this->cacheDirectory = $cacheDirectory;
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
        ];
        if ($this->httpCache) { // Actually useless unless http cache is enabled
            $params['handler'] = $this->getClientHandlerStack();
        }
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
        // Create a HandlerStack
        $stack = HandlerStack::create();
        // Add cache middleware to the top of the stack with `push`
        $stack->push(
            new CacheMiddleware(
                new PrivateCacheStrategy(
                    new Psr6CacheStorage(
                        new FilesystemAdapter('api-client-http-cache', 600, $this->cacheDirectory)
                    )
                )
            ),
            'cache'
        );

        return $stack;
    }
}
