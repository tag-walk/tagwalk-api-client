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
use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\Promise\PromiseInterface;
use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagwalk\ApiClientBundle\Exception\ApiAccessDeniedException;
use Tagwalk\ApiClientBundle\Exception\ApiServerErrorException;
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
     * @param string $showroom
     */
    public function setShowroom(string $showroom): void
    {
        $this->showroom = $showroom;
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return ResponseInterface
     *
     * @throws ApiServerErrorException
     * @throws ApiAccessDeniedException
     * @throws NotFoundHttpException
     */
    public function request($method, $uri, $options = []): ResponseInterface
    {
        $options = array_replace_recursive($this->getDefaultOptions(), $options);
        $this->logger->debug('ApiProvider::request', compact('method', 'uri', 'options'));
        $response = $this->clientFactory->get()->request($method, $uri, $options);
        $this->logger->debug('ApiProvider::request::response', [
            'message' => (string) $response->getBody(),
            'code'    => $response->getStatusCode(),
        ]);
        switch ($response->getStatusCode()) {
            case Response::HTTP_FORBIDDEN:
                $this->logger->warning('ApiProvider request access denied');

                throw new ApiAccessDeniedException();
            case Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE:
                $this->logger->warning('ApiProvider request out of range');

                throw new NotFoundHttpException('Out of range');
            case Response::HTTP_SERVICE_UNAVAILABLE:
                $this->logger->warning('ApiProvider request service unavailable');

                throw new ApiServerErrorException();
            case Response::HTTP_UNAUTHORIZED:
                $this->logger->warning('ApiProvider request unauthorized');
                if (strpos($uri, 'login') === false) {
                    $this->apiTokenStorage->clearCachedToken();
                }

                throw new ApiAccessDeniedException();
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                $this->logger->error('ApiProvider request internal server error');

                throw new ApiServerErrorException();
        }

        return $response;
    }

    /**
     * @return array
     */
    private function getDefaultOptions(): array
    {
        try {
            // get oauth2 token for request header
            $token = $this->apiTokenStorage->getAccessToken();
        } catch (ClientException $exception) {
            $this->logger->warning('ApiTokenStorage::getAccessToken unauthorized error');
            $this->apiTokenStorage->clearCachedToken();

            throw new ApiAccessDeniedException();
        }
        $locale = $this->requestStack->getCurrentRequest()
            ? $this->requestStack->getCurrentRequest()->getLocale() ?? 'en'
            : 'en';
        $headers = array_filter([
            'Accept'                => 'application/json',
            'Accept-Language'       => $locale,
            'Authorization'         => $token !== null ? sprintf('Bearer %s', $token) : null,
            'Analytics'             => $this->analytics,
            'Tagwalk-Showroom-Name' => $this->showroom,
        ]);
        // Showroom clients specific headers
        if ($this->showroom !== null) {
            $headers['Tagwalk-Showroom-Name'] = $this->showroom;
        }

        return [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS     => $headers,
            RequestOptions::QUERY       => [
                'light' => $this->lightData,
            ],
        ];
    }

    /**
     * @param string $method
     * @param string $uri
     * @param array  $options
     *
     * @return PromiseInterface
     *
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
