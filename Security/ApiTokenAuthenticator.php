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
     * @var string|null
     */
    private $redirectUri;

    /**
     * @var string|null
     */
    private $showroom;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ClientFactory        $clientFactory
     * @param SessionInterface     $session
     * @param string               $clientId
     * @param string               $clientSecret
     * @param string|null          $redirectUri
     * @param string|null          $showroom
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ClientFactory $clientFactory,
        SessionInterface $session,
        string $clientId,
        string $clientSecret,
        ?string $redirectUri = null,
        ?string $showroom = null,
        LoggerInterface $logger = null
    ) {
        $this->clientFactory = $clientFactory;
        $this->session = $session;
        $this->clientId = $clientId;
        $this->clientSecret = $clientSecret;
        $this->redirectUri = $redirectUri;
        $this->showroom = $showroom;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * @param string $clientId
     *
     * @return self
     */
    final public function setClientId(string $clientId): self
    {
        $this->clientId = $clientId;

        return $this;
    }

    /**
     * @param string $clientSecret
     *
     * @return self
     */
    final public function setClientSecret(string $clientSecret): self
    {
        $this->clientSecret = $clientSecret;

        return $this;
    }

    /**
     * @param string|null $redirectUri
     *
     * @return self
     */
    final public function setRedirectUri(?string $redirectUri): self
    {
        $this->redirectUri = $redirectUri;

        return $this;
    }

    /**
     * @param string|null $showroom
     *
     * @return self
     */
    final public function setShowroom(?string $showroom): self
    {
        $this->showroom = $showroom;

        return $this;
    }

    /**
     * Authenticate to API and returns response as array
     *
     * @return array
     */
    final public function authenticate(): array
    {
        $params = [
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'grant_type'    => 'client_credentials',
        ];
        $this->logger->info('ApiTokenAuthenticator::authenticate', $params);
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

    /**
     * @param string $token
     *
     * @return array
     */
    final public function refreshToken(string $token): array
    {
        $params = [
            'grant_type'    => 'refresh_token',
            'client_id'     => $this->clientId,
            'client_secret' => $this->clientSecret,
            'refresh_token' => $token,
        ];
        $this->logger->info('ApiTokenAuthenticator::refreshToken', $params);
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

    /**
     * @param string $code
     * @param string $userToken
     *
     * @return array
     */
    final public function authorize(string $code, string $userToken): array
    {
        try {
            $this->logger->info('ApiTokenAuthenticator::authorize', [
                'code'       => $code,
                'user_token' => $userToken,
            ]);
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
                        'X-AUTH-TOKEN'          => $userToken,
                        'Tagwalk-Showroom-Name' => $this->showroom,
                    ]),
                    RequestOptions::HTTP_ERRORS => true,
                ]
            );
        } catch (ClientException $exception) {
            $this->logger->error('Error authorizing token', [
                'user_token' => $userToken,
                'response'   => $exception->getResponse() !== null ? json_decode(
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

    /**
     * @param string|null $userToken
     *
     * @return array
     */
    final public function getAuthorizationQueryParameters(?string $userToken): array
    {
        $state = hash('sha512', random_bytes(32));
        $this->session->set(self::AUTHORIZATION_STATE, $state);

        return array_filter([
            'response_type'         => 'code',
            'state'                 => $state,
            'client_id'             => $this->clientId,
            'redirect_uri'          => $this->redirectUri,
            'x-auth-token'          => $userToken,
            'tagwalk-showroom-name' => $this->showroom,
        ]);
    }
}
