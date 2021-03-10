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
use Psr\Cache\CacheItemInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Symfony\Contracts\Cache\ItemInterface;

class ApiTokenStorage
{
    /** @var int default access token time to live: 1 hour */
    public const DEFAULT_ACCESS_TOKEN_TTL = 3600;

    /** @var int default refresh token time to live: 1 year */
    public const DEFAULT_REFRESH_TOKEN_TTL = 31536000;

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
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @var string
     */
    private $identifier;

    /**
     * @var string
     */
    private $cachePrefix = 'TW@';

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param ApiTokenAuthenticator $authenticator
     * @param string|null           $cacheDirectory
     * @param LoggerInterface|null  $logger
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        ApiTokenAuthenticator $authenticator,
        ?string $cacheDirectory = null,
        LoggerInterface $logger = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->authenticator = $authenticator;
        $this->accessTokenCache = new FilesystemAdapter('access_token', self::DEFAULT_ACCESS_TOKEN_TTL, $cacheDirectory);
        $this->refreshTokenCache = new FilesystemAdapter('refresh_token', self::DEFAULT_REFRESH_TOKEN_TTL, $cacheDirectory);
        $this->logger = $logger ?? new NullLogger();
    }

    public function setCachePrefix(string $cachePrefix): void
    {
        $this->cachePrefix = $cachePrefix;
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
        $this->identifier = md5($this->cachePrefix.$username);
        $this->logger->debug('ApiTokenStorage::Init', [
            'class'    => $token !== null ? get_class($token) : null,
            'username' => $username,
            'token_id' => $this->identifier,
        ]);
    }

    /**
     * Clear existing token
     */
    public function clearCachedToken(): void
    {
        $this->logger->notice('Clearing token from ApiTokenStorage');
        $this->accessTokenCache->delete($this->identifier);
        $this->refreshTokenCache->delete($this->identifier);
    }

    /**
     * Returns a valid access token for the current user
     *
     * Fetch a new access_token or use a refresh_token to restore an old session
     *
     * @return string
     */
    public function getAccessToken(): string
    {
        $credentials = null;
        if (null === $this->identifier) {
            $this->init();
        }
        $cacheKey = $this->identifier;
        /** @var float $beta probabilistic early expiration, disable early recompute when refresh token is available and boost early recomputation on anonymous token */
        $beta = $this->refreshTokenCache->getItem($cacheKey)->isHit() ? 0 : 2.0;
        $accessToken = $this->accessTokenCache->get($cacheKey, function (ItemInterface $item) use ($cacheKey) {
            $dateTime = new DateTime();
            // check for refresh token in cache storage
            /** @var CacheItemInterface $tokenToRefreshCacheItem */
            $tokenToRefreshCacheItem = $this->refreshTokenCache->getItem($cacheKey);
            if ($tokenToRefreshCacheItem->isHit()) {
                // Use refresh token to authenticate
                $tokenToRefresh = $tokenToRefreshCacheItem->get();
                $authentication = $this->authenticator->refreshToken($tokenToRefresh);
            } else {
                // create a new anonymous token
                $authentication = $this->authenticator->authenticate();
            }
            // save the refresh token in his storage
            if (isset($authentication['refresh_token'])) {
                $refreshTokenExpiration = (clone $dateTime)->modify('+1 year');
                $tokenToRefreshCacheItem->set($authentication['refresh_token']);
                $tokenToRefreshCacheItem->expiresAt($refreshTokenExpiration);
                $this->refreshTokenCache->save($tokenToRefreshCacheItem);
            }
            // set cache item expiration from response
            if (isset($authentication['expires_in'])) {
                $accessTokenExpiration = (clone $dateTime)->add(new DateInterval(sprintf('PT%dS', (int) $authentication['expires_in'])));
                $this->logger->debug('access_token cache item will expires at '.$accessTokenExpiration->format(DATE_ATOM));
                $item->expiresAt($accessTokenExpiration);
            } else {
                $this->logger->debug(sprintf('access_token cache item will expires in %d seconds', self::DEFAULT_ACCESS_TOKEN_TTL));
                $item->expiresAfter(self::DEFAULT_ACCESS_TOKEN_TTL);
            }

            return $authentication['access_token'];
        }, $beta);

        return $accessToken;
    }

    /**
     * Save access token in cache storage
     *
     * @param string|null $accessToken
     * @param int         $expiresIn
     */
    public function setAccessToken(?string $accessToken, int $expiresIn = self::DEFAULT_ACCESS_TOKEN_TTL): void
    {
        if (null === $this->identifier) {
            $this->init();
        }

        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->accessTokenCache->getItem($this->identifier);
        $cacheItem->set($accessToken);
        $cacheItem->expiresAfter(new DateInterval(sprintf('PT%dS', $expiresIn)));
        $this->accessTokenCache->save($cacheItem);
    }

    /**
     * Save refresh token in cache storage
     *
     * @param string|null $refreshToken
     * @param int         $expiresIn
     */
    public function setRefreshToken(?string $refreshToken, int $expiresIn = self::DEFAULT_REFRESH_TOKEN_TTL): void
    {
        if (null === $this->identifier) {
            $this->init();
        }

        /** @var CacheItemInterface $cacheItem */
        $cacheItem = $this->refreshTokenCache->getItem($this->identifier);
        $cacheItem->set($refreshToken);
        $cacheItem->expiresAfter(new DateInterval(sprintf('PT%dS', $expiresIn)));
        $this->refreshTokenCache->save($cacheItem);
    }
}
