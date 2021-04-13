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

namespace Tagwalk\ApiClientBundle\Security;

use GuzzleHttp\Exception\ClientException;
use GuzzleHttp\RequestOptions;
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Tagwalk\ApiClientBundle\Factory\ClientFactory;

class ApiTokenAuthenticator
{
    /** @var string session key for authorization state value */
    public const AUTHORIZATION_STATE = 'auth_state';

    /**
     * @var ClientFactory
     */
    private $clientFactory;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var string
     */
    private $clientId;

    /**
     * @var string
     */
    private $clientSecret;

    /**
     * @var bool
     */
    private $authenticateInShowroom;

    /**
     * @var string|null
     */
    private $redirectUri;

    private ?string $applicationName = null;

    /**
     * @var LoggerInterface
     */
    private $logger;

    public function __construct(
        ClientFactory $clientFactory,
        SessionInterface $session,
        string $clientId,
        string $clientSecret,
        bool $authenticateInShowroom = false,
        ?string $redirectUri = null,
        LoggerInterface $logger = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->session = $session;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->authenticateInShowroom = $authenticateInShowroom;
        $this->redirectUri = $redirectUri;
        $this->logger = $logger ?? new NullLogger();
    }

    public function setApplicationName(string $applicationName): void
    {
        $this->applicationName = $applicationName;
    }

    final public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    final public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    final public function setAuthenticateInShowroom(bool $authenticateInShowroom): self
    {
        $this->authenticateInShowroom = $authenticateInShowroom;

        return $this;
    }

    final public function setRedirectUri(?string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    final public function authenticate(): array
    {
        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        ];
        $response = $this->clientFactory->get()->request(
            'POST',
            '/oauth/v2/token',
            [
                RequestOptions::FORM_PARAMS => $params,
                RequestOptions::HTTP_ERRORS => true,
            ]
        );

        return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    final public function refreshToken(string $token): array
    {
        $params = [
            'grant_type'    => 'refresh_token',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $token,
        ];
        $response = $this->clientFactory->get()->request(
            'POST',
            '/oauth/v2/token',
            [
                RequestOptions::FORM_PARAMS => $params,
                RequestOptions::HTTP_ERRORS => true,
            ]
        );

        return json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
    }

    final public function authorize(string $code, string $userToken): array
    {
        $this->setApplicationFromSession();

        try {
            $response = $this->clientFactory->get()->request(
                'POST',
                '/oauth/v2/token',
                [
                    RequestOptions::FORM_PARAMS => [
                        'grant_type'    => 'authorization_code',
                        'client_id'     => $this->clientId,
                        'client_secret' => $this->clientSecret,
                        'redirect_uri'  => $this->redirectUri,
                        'code'          => $code,
                    ],
                    RequestOptions::HEADERS     => array_filter([
                        'X-AUTH-TOKEN'             => $userToken,
                        'Tagwalk-Application-Name' => $this->applicationName,
                        'Authenticate-In-Showroom' => $this->authenticateInShowroom
                    ]),
                    RequestOptions::HTTP_ERRORS => true,
                ]
            );
        } catch (ClientException $exception) {
            $this->logger->error('Error authorizing token', [
                'response' => $exception->getResponse() !== null ? json_decode(
                    $exception->getResponse()->getBody(),
                    true,
                    512,
                    JSON_THROW_ON_ERROR
                ) : null,
            ]);

            throw $exception;
        }

        $jsonDecode = json_decode((string)$response->getBody(), true, 512, JSON_THROW_ON_ERROR);
        if (isset($jsonDecode['state']) && $jsonDecode['state'] !== $this->session->get(self::AUTHORIZATION_STATE)) {
            throw new InvalidArgumentException('Incorrect state value.');
        }

        return $jsonDecode;
    }

    final public function getAuthorizationQueryParameters(?string $userToken): array
    {
        $state = hash('sha512', random_bytes(32));
        $this->session->set(self::AUTHORIZATION_STATE, $state);
        $this->setApplicationFromSession();

        return array_filter([
            'response_type'            => 'code',
            'state'                    => $state,
            'client_id'                => $this->clientId,
            'redirect_uri'             => $this->redirectUri,
            'x-auth-token'             => $userToken,
            'tagwalk-application-name' => $this->applicationName,
            'authenticate-in-showroom' => $this->authenticateInShowroom,
        ]);
    }

    public function setApplicationFromSession(): void
    {
        $applicationName = $this->session->get('user-application');

        if (!empty($applicationName)) {
            $this->applicationName = $applicationName;
            $this->setAuthenticateInShowroom(true);
        }
    }
}
