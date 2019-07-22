<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Http\Message\ResponseInterface;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class UserManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var SerializerInterface|Serializer
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider         $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->logger = new NullLogger();
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function get(string $email): ?User
    {
        $user = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/users/'.$email, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $user = $this->deserialize($apiResponse);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('UserManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $user;
    }

    /**
     * @param ResponseInterface $response
     *
     * @return User
     */
    private function deserialize($response): User
    {
        return $this->serializer->deserialize(
            $response->getBody()->getContents(),
            User::class,
            JsonEncoder::FORMAT
        );
    }

    /**
     * @param User $user
     *
     * @return User|null
     */
    public function create(User $user): ?User
    {
        $data = $this->serializer->normalize($user, null, ['registration' => true]);
        $apiResponse = $this->apiProvider->request('POST', '/api/users/register', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $data,
        ]);
        $created = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $created = $this->deserialize($apiResponse);
        } else if ($apiResponse->getStatusCode() === Response::HTTP_CONFLICT){
            $this->logger->notice('User already exists', [
               'code' => $apiResponse->getStatusCode(),
               'message' => $apiResponse->getBody()->getContents(),
            ]);
        } else {
            $this->logger->error('UserManager::create unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $created;
    }

    /**
     * @param string $email
     * @param User   $user
     *
     * @return User|null
     */
    public function update(string $email, User $user): ?User
    {
        $data = $this->serializer->normalize($user, null, ['account' => true]);
        $data = array_filter($data, static function ($v) {
            return $v !== null;
        });
        $apiResponse = $this->apiProvider->request('PATCH', '/api/users', [
            RequestOptions::QUERY       => ['email' => $email],
            RequestOptions::JSON        => $data,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $updated = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $updated = $this->deserialize($apiResponse);
        } else {
            $this->logger->error('UserManager::update unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $updated;
    }

    /**
     * @param string $property
     * @param string $value
     *
     * @return User|null
     */
    public function findBy(string $property, string $value): ?User
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/users/find', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => [
                'key'   => $property,
                'value' => $value,
            ],
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = $this->deserialize($apiResponse);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('UserManager::findBy unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }
}
