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
use GuzzleHttp\RequestOptions;
use OutOfBoundsException;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\RequestStack;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Tagwalk\ApiClientBundle\Exception\ApiAccessDeniedException;
use Tagwalk\ApiClientBundle\Exception\ApiLoginFailedException;
use Tagwalk\ApiClientBundle\Exception\ApiServerErrorException;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;
use Tagwalk\ApiClientBundle\Security\ApiTokenStorage;

class ApiProvider
{
    private ClientFactory $clientFactory;
    private RequestStack $requestStack;
    private bool $lightData;
    private bool $analytics;
    private bool $authenticateInShowroom;
    private SessionInterface $session;
    private ?string $applicationName = null;
    private ?LoggerInterface $logger;
    private ApiTokenStorage $apiTokenStorage;

    public function __construct(
        ClientFactory $clientFactory,
        RequestStack $requestStack,
        ApiTokenStorage $apiTokenStorage,
        SessionInterface $session,
        bool $lightData = true,
        bool $analytics = false,
        bool $authenticateInShowroom = false,
        LoggerInterface $logger = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->requestStack = $requestStack;
        $this->apiTokenStorage = $apiTokenStorage;
        $this->session = $session;
        $this->lightData = $lightData;
        $this->analytics = $analytics;
        $this->authenticateInShowroom = $authenticateInShowroom;
        $this->logger = $logger ?? new NullLogger();
    }

    public function setApplicationName(string $applicationName): void
    {
        $this->applicationName = $applicationName;
    }

    final public function setAuthenticateInShowroom(bool $authenticateInShowroom): self
    {
        $this->authenticateInShowroom = $authenticateInShowroom;

        return $this;
    }

    /**
     * @throws ApiAccessDeniedException
     * @throws ApiLoginFailedException
     * @throws ApiServerErrorException
     * @throws NotFoundHttpException
     */
    final public function request(string $method, string $uri, array $options = []): ResponseInterface
    {
        $options = array_replace_recursive($this->getDefaultOptions(), $options);
        $data = compact('method', 'uri', 'options');
        $this->logger->debug('ApiProvider::request', $this->filterSensitiveData($data));
        $response = $this->clientFactory->get()->request($method, $uri, $options);
        $this->logger->debug('ApiProvider::request::response', [
            'message' => substr($response->getBody(), 0, 1024),
            'code'    => $response->getStatusCode(),
        ]);
        switch ($response->getStatusCode()) {
            case Response::HTTP_FORBIDDEN:
                $this->logger->warning('ApiProvider request access denied');
                if (strpos($uri, 'login') !== false) {
                    throw new ApiLoginFailedException((string)$response->getBody(), Response::HTTP_FORBIDDEN);
                }

                throw new ApiAccessDeniedException();
            case Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE:
                $this->logger->warning('ApiProvider request out of range');

                throw new OutOfBoundsException('Out of bounds');
            case Response::HTTP_SERVICE_UNAVAILABLE:
                $this->logger->warning('ApiProvider request service unavailable');

                throw new ApiServerErrorException();
            case Response::HTTP_UNAUTHORIZED:
                $this->logger->warning('ApiProvider request unauthorized');
                if (strpos($uri, 'login') === false) {
                    $this->apiTokenStorage->clearCachedToken();
                } else {
                    throw new ApiLoginFailedException('Bad credentials', Response::HTTP_UNAUTHORIZED);
                }

                throw new ApiAccessDeniedException();
            case Response::HTTP_INTERNAL_SERVER_ERROR:
                $this->logger->error('ApiProvider request internal server error');

                throw new ApiServerErrorException();
            case Response::HTTP_CONFLICT:
                $this->logger->warning('ApiProvider request conflict');
        }

        return $response;
    }

    private function getDefaultOptions(): array
    {
        $userApplication = $this->session->get('user-application');
        if ($userApplication) {
            $this->setAuthenticateInShowroom(true);
            $this->setApplicationName($userApplication);
        }

        try {
            // get oauth2 token for request header
            $token = $this->apiTokenStorage->getAccessToken();
        } catch (ClientException $exception) {
            $response = $exception->getResponse();
            $this->logger->warning('ApiTokenStorage::getAccessToken unauthorized error', [
                'exception' => get_class($exception),
                'code'      => $response ? $response->getStatusCode() : null,
                'message'   => $response ? (string)$response->getBody() : null,
            ]);
            $this->apiTokenStorage->clearCachedToken();

            throw new ApiAccessDeniedException();
        }
        $locale = $this->requestStack->getCurrentRequest()
            ? $this->requestStack->getCurrentRequest()->getLocale() ?? 'en'
            : 'en';
        $headers = array_filter([
            'Accept'                   => 'application/json',
            'Accept-Language'          => $locale,
            'Authorization'            => $token !== null ? sprintf('Bearer %s', $token) : null,
            'Analytics'                => (int)$this->analytics,
            'Tagwalk-Application-Name' => $this->applicationName,
            'Authenticate-In-Showroom' => $this->authenticateInShowroom,
        ], static function ($item) {
            return $item !== null;
        });

        return [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::HEADERS     => $headers,
            RequestOptions::QUERY       => [
                'light' => $this->lightData,
            ],
        ];
    }

    final public function getClient(): Client
    {
        return $this->clientFactory->get();
    }

    private function filterSensitiveData(array &$data): array
    {
        $sensitiveParams = ['Authorization', 'password', 'salt', 'api_token', 'token', 'access_token'];

        foreach ($data as $key => &$value) {
            if (is_array($value) === true) {
                $this->filterSensitiveData($value);

                continue;
            }

            if (in_array($key, $sensitiveParams) === true) {
                unset($data[$key]);
            }
        }

        return $data;
    }
}
