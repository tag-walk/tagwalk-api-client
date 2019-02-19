<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use Symfony\Component\Serializer\Serializer;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Symfony\Component\HttpFoundation\Response;

class UserManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
     */
    private $serializer;

    /**
     * @param ApiProvider $apiProvider
     * @param Serializer $serializer
     */
    public function __construct(ApiProvider $apiProvider, Serializer $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param User $user
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function create(User $user)
    {
        $data = $this->serializer->normalize($user, null, ['registration' => true]);
        $apiResponse = $this->apiProvider->request('POST', '/api/users/registration', [
            'json' => $data,
            'http_errors' => false
        ]);

        return $apiResponse;
    }

    /**
     * @param string $email
     * @param User $user
     * @return Response
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Symfony\Component\Serializer\Exception\ExceptionInterface
     */
    public function update(string $email, User $user)
    {
        $data = $this->serializer->normalize($user, null, ['account' => true]);
        $data = array_filter($data, function($v) { return !is_null($v); });
        $apiResponse = $this->apiProvider->request('PATCH', '/api/users', [
            'query' => ['email' => $email],
            'json' => $data,
            'http_errors' => false
        ]);

        return $apiResponse;
    }
}