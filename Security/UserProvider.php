<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Security;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\ServiceUnavailableHttpException;
use Symfony\Component\Security\Core\Exception\UnsupportedUserException;
use Symfony\Component\Security\Core\Exception\UsernameNotFoundException;
use Symfony\Component\Security\Core\User\UserInterface;
use Symfony\Component\Security\Core\User\UserProviderInterface;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class UserProvider implements UserProviderInterface
{
    /**
     * @var ApiProvider
     */
    private $provider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface|null
     */
    private $logger;

    /**
     * @param ApiProvider          $provider
     * @param SerializerInterface  $serializer
     * @param LoggerInterface|null $logger
     */
    public function __construct(ApiProvider $provider, SerializerInterface $serializer, LoggerInterface $logger = null)
    {
        $this->provider = $provider;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * {@inheritdoc}
     */
    public function loadUserByUsername($username)
    {
        $response = $this->provider->request('GET', '/api/users/'.strtolower($username), [RequestOptions::HTTP_ERRORS => false]);
        if ($response->getStatusCode() === Response::HTTP_NOT_FOUND) {
            throw new UsernameNotFoundException(sprintf('user not found with %s', $username));
        }
        if ($response->getStatusCode() !== Response::HTTP_OK) {
            $this->logger->error('UserProvider::loadUserByUsername error ', [
                'username' => $username,
                'message'  => (string) $response->getBody(),
                'code'     => $response->getStatusCode(),
            ]);

            throw new ServiceUnavailableHttpException('Unable to connect');
        }
        /** @var User $user */
        $user = $this->serializer->deserialize($response->getBody(), User::class, JsonEncoder::FORMAT);

        return $user;
    }

    /**
     * {@inheritdoc}
     */
    public function refreshUser(UserInterface $user): UserInterface
    {
        if (!$user instanceof User) {
            throw new UnsupportedUserException(
                sprintf('Instances of "%s" are not supported.', get_class($user))
            );
        }

        return $user;
    }

    /**
     * Whether this provider supports the given user class.
     *
     * @param string $class
     *
     * @return bool
     */
    public function supportsClass($class): bool
    {
        return User::class === $class;
    }
}
