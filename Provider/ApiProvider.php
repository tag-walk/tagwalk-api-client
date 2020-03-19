<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Provider;

use GuzzleHttp\Client;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Exception\ApiAccessDeniedException;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;
use Tagwalk\ApiClientBundle\Security\ApiTokenStorage;

class ApiProvider
{
    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var RequestStack
     */
    private $requestStack;

    /**
     * @var bool
     */
    private $lightData;

    /**
     * @var bool
     */
    private $analytics;

    /**
     * @var string
     */
    private $showroom;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var ApiTokenStorage
     */
    private $apiTokenStorage;

    /**
     * @param ClientFactory        $clientFactory
     * @param RequestStack         $requestStack
     * @param ApiTokenStorage      $apiTokenStorage
     * @param bool                 $lightData do not resolve files path property
     * @param bool                 $analytics
     * @param string|null          $showroom
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ClientFactory $clientFactory,
        RequestStack $requestStack,
        ApiTokenStorage $apiTokenStorage,
        bool $lightData = true,
        bool $analytics = false,
        ?string $showroom = null,
        LoggerInterface $logger = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->requestStack = $requestStack;
        $this->apiTokenStorage = $apiTokenStorage;
        $this->lightData = $lightData;
        $this->analytics = $analytics;
        $this->showroom = $showroom;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     */
    public function request($method, $uri, $options = []): ResponseInterface
    {
        $options = array_replace_recursive($this->getDefaultOptions(), $options);
        $this->logger->debug('requesting api', compact('method', 'uri', 'options'));
        $response = $this->clientFactory->get()->request($method, $uri, $options);
        if (strpos($uri, 'login') === false) {
            switch ($response->getStatusCode()) {
                case Response::HTTP_UNAUTHORIZED:
                    $this->logger->error('Tagwalk API unauthorized error');
                    // invalidate token
                    $this->apiTokenStorage->clearAccessToken();
                    // recreate a fresh one
                    $this->apiTokenStorage->getAccessToken();
                    // Retry the request
                    $response = $this->clientFactory->get()->request($method, $uri, $options);
                    break;
                case Response::HTTP_FORBIDDEN:
                    throw new ApiAccessDeniedException();
            }
        }

        return $response;
    }

    /**
     * @return array
     */
    private function getDefaultOptions(): array
    {
        $headers = array_filter([
            'Accept'          => 'application/json',
            'Accept-Language' => $this->requestStack->getCurrentRequest()
                ? $this->requestStack->getCurrentRequest()->getLocale()
                : 'en',
        ]);
        // oauth2 token specific headers
        $token = $this->apiTokenStorage->getAccessToken();
        if ($token !== null) {
            $headers['Authorization'] = sprintf('Bearer %s', $token);
        }
        // Showroom clients specific headers
        if ($this->showroom !== null) {
            $headers['Tagwalk-Showroom-Name'] = $this->showroom;
        }

        return [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS     => $headers,
            RequestOptions::QUERY       => [
                'light'     => $this->lightData,
                'analytics' => $this->analytics,
            ],
        ];
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return PromiseInterface
     * @deprecated request logic not implemented
     */
    public function requestAsync($method, $uri, $options = []): PromiseInterface
    {
        $options = array_merge($this->getDefaultOptions(), $options);

        return $this->clientFactory->get()->requestAsync($method, $uri, $options);
    }

    /**
     * @return Client
     */
    public function getClient(): Client
    {
        return $this->clientFactory->get();
    }
}
