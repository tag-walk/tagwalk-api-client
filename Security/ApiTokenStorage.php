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

use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\Cache\Adapter\FilesystemAdapter;
use Symfony\Component\HttpFoundation\Session\SessionInterface;
use Symfony\Component\Security\Core\Authentication\Token\Storage\TokenStorageInterface;
use Tagwalk\ApiClientBundle\Model\User;

class ApiTokenStorage
{
    /**
     * @var TokenStorageInterface
     */
    private $tokenStorage;

    /**
     * @var FilesystemAdapter
     */
    private $cache;

    /**
     * @var string
     */
    private $tokenId;

    /**
     * @var SessionInterface
     */
    private $session;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @param TokenStorageInterface $tokenStorage
     * @param SessionInterface      $session
     * @param LoggerInterface|null  $logger
     */
    public function __construct(
        TokenStorageInterface $tokenStorage,
        SessionInterface $session,
        LoggerInterface $logger = null
    ) {
        $this->tokenStorage = $tokenStorage;
        $this->session = $session;
        $this->cache = new FilesystemAdapter('api-client-token-storage');
        $this->logger = $logger ?? new NullLogger();
    }


    /**
     * Initilization
     */
    public function init(): void
    {
        $token = $this->tokenStorage->getToken();
        $username = $token === null ? 'anon.' : $token->getUsername();
        $this->tokenId = md5($username);
        $this->logger->debug('ApiTokenStorage::Init', [
            'class'    => $token !== null ? get_class($token) : null,
            'username' => $username,
            'token_id' => $this->tokenId,
        ]);
    }

    /**
     * Clear existing token
     */
    public function clear(): void
    {
        $this->cache->delete($this->tokenId);
    }

    /**
     * @return ApiCredentials|null
     */
    public function get(): ?ApiCredentials
    {
        if (null === $this->tokenId) {
            $this->init();
        }

        return $this->cache->hasItem($this->tokenId) ? $this->cache->getItem($this->tokenId)->get() : null;
    }

    public function reload(): ApiCredentials
    {
        return $this->cache->getItem($this->tokenId)->get();
    }

    /**
     * Load token credentials from api response
     *
     * @param array $response
     *
     * @return ApiCredentials
     */
    public function save(array $response): ApiCredentials
    {
        $credentials = $this->get() ?? new ApiCredentials();
        $credentials = $credentials->denormalize($response);
        if ($credentials->getUserToken() === null) {
            $token = $this->tokenStorage->getToken();
            $user = $token !== null ? $token->getUser() : null;
            if ($user !== null && is_object($user) && $user instanceof User) {
                $credentials->setUserToken($user->getApiToken());
            }
        }
        $cacheItem = $this->cache->getItem($this->tokenId);
        $cacheItem->set($credentials);
        $this->cache->save($cacheItem);

        return $credentials;
    }
}
