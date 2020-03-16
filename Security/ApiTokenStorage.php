<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Security;

use DateInterval;
use DateTime;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ApiTokenStorage
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var ApiTokenAuthenticator
     */
    private $authenticator;

    /**
     * @var FilesystemAdapter
     */
    private $accessTokenCache;

    /**
     * @var FilesystemAdapter
     */
    private $refreshTokenCache;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ApiTokenAuthenticator $authenticator
     * @param LoggerInterface|null  $logger
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ApiTokenAuthenticator $authenticator,
        LoggerInterface $logger = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticator = $authenticator;
        $this->logger = $logger ?? new NullLogger();
        $this->accessTokenCache = new FilesystemAdapter('access_token');
        $this->refreshTokenCache = new FilesystemAdapter('refresh_token');
    }

    /**
     * Initilize storage key
     *
     * @param string|null $username
     */
    public function init(?string $username = null): void
    {
        $token = $this->tokenStorage->getToken();
        if (null === $username) {
            $username = $token === null ? 'anon.' : $token->getUsername();
        }
        $this->identifier = md5($username);
        $this->logger->debug('ApiTokenStorage::Init', [
            'class'    => $token !== null ? get_class($token) : null,
            'username' => $username,
            'token_id' => $this->identifier,
        ]);
    }

    /**
     * Clear existing token
     */
    public function clearAccessToken(): void
    {
        $this->logger->notice('Clearing token from ApiTokenStorage');
        $this->accessTokenCache->delete($this->identifier);
    }

    /**
     * Returns a valid access token for the current user
     *
     * Fetch a new access_token or use a refresh_token to restore an old session
     *
     * @return ApiCredentials
     */
    public function getAccessToken(): string
    {
        $credentials = null;
        if (null === $this->identifier) {
            $this->init();
        }
        $this->logger->debug('ApiTokenStorage::getAccessToken', ['identifier' => $this->identifier]);
        $cacheKey = $this->identifier;

        return $this->accessTokenCache->get($this->identifier, function (ItemInterface $item) use ($cacheKey) {
            $dateTime = new DateTime();
            $tokenToRefreshCacheItem = $this->refreshTokenCache->getItem($cacheKey);
            if ($tokenToRefreshCacheItem->isHit()) {
                /** @var ApiCredentials $tokenToRefresh */
                $tokenToRefresh = $tokenToRefreshCacheItem->get();
                $authentication = $this->authenticator->refreshToken($tokenToRefresh);
            } else {
                $authentication = $this->authenticator->authenticate();
            }
            if (isset($authentication['expires_in'])) {
                $expiration = new DateInterval(sprintf('PT%dS', (int) $authentication['expires_in']));
                $item->expiresAt($dateTime->add($expiration));
            }
            // save token to refresh token storage as well
            if (isset($authentication['refresh_token'])) {
                $tokenToRefreshCacheItem->set($authentication['refresh_token']);
                $tokenToRefreshCacheItem->expiresAt($dateTime->modify('+1 year'));
                $this->refreshTokenCache->save($tokenToRefreshCacheItem);
            }

            $this->logger->debug('ApiTokenStorage::getAccessToken creating cache fron api response', $authentication);

            return $authentication['access_token'];
        });
    }

    /**
     * Save access token in cache storage
     *
     * @param string|null $accessToken
     */
    public function setAccessToken(?string $accessToken): void
    {
        if (null === $this->identifier) {
            $this->init();
        }
        $this->logger->debug('ApiTokenStorage::setAccessToken', [
            'identifier'   => $this->identifier,
            'access_token' => $accessToken,
        ]);
        $cacheItem = $this->accessTokenCache->getItem($this->identifier);
        $cacheItem->set($accessToken);
        $this->accessTokenCache->save($cacheItem);
    }

    /**
     * Save refresh token in cache storage
     *
     * @param string|null $refreshToken
     */
    public function setRefreshToken(?string $refreshToken): void
    {
        if (null === $this->identifier) {
            $this->init();
        }
        $this->logger->debug('ApiTokenStorage::setRefreshToken', [
            'identifier'    => $this->identifier,
            'refresh_token' => $refreshToken,
        ]);
        $cacheItem = $this->refreshTokenCache->getItem($this->identifier);
        $cacheItem->set($refreshToken);
        $this->refreshTokenCache->save($cacheItem);
    }
}
