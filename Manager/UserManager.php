<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use Psr\Http\Message\ResponseInterface;
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
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param string $email
     * @return User|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     */
    public function get(string $email)
    {
        $user = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/users/' . $email, ['http_errors' => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $user = $this->deserialize($apiResponse);
        }

        return $user;
    }

    /**
     * @param ResponseInterface $response
     * @return User
     */
    private function deserialize($response)
    {
        return $this->serializer->deserialize(
            $response->getBody()->getContents(),
            User::class,
            JsonEncoder::FORMAT
        );
    }

    /**
     * @param User $user
     * @return User|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function create(User $user)
    {
        $data = $this->serializer->normalize($user, null, ['registration' => true]);
        $apiResponse = $this->apiProvider->request('POST', '/api/users/register', [
            'json' => $data,
            'http_errors' => false
        ]);
        $created = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $created = $this->deserialize($apiResponse);
        }

        return $created;
    }

    /**
     * @param string $email
     * @param User $user
     * @return User|null
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function update(string $email, User $user)
    {
        $data = $this->serializer->normalize($user, null, ['account' => true]);
        $data = array_filter($data, function ($v) {
            return $v !== null;
        });
        $apiResponse = $this->apiProvider->request('PATCH', '/api/users', [
            'query' => ['email' => $email],
            'json' => $data,
            'http_errors' => false
        ]);
        $updated = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $updated = $this->deserialize($apiResponse);
        }

        return $updated;
    }
}