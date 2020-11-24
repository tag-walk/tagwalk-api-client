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
use InvalidArgumentException;
use Psr\Http\Message\ResponseInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Exception\AccountAlreadyActivatedException;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class UserManager
{
    public ?string $lastError = null;

    private ApiProvider $apiProvider;
    /** @var Serializer $serializer */
    private $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function get(string $email): ?User
    {
        $user = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/users/' . $email,
            [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $user = $this->deserialize($apiResponse);
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
        /** @var User $user */
        $user = $this->serializer->deserialize(
            (string)$response->getBody(),
            User::class,
            JsonEncoder::FORMAT
        );

        return $user;
    }

    public function create(User $user): ?User
    {
        $data = $this->serializer->normalize($user, null, ['write' => true]);
        $apiResponse = $this->apiProvider->request('POST', '/api/users/register', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $data,
        ]);
        $created = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $created = $this->deserialize($apiResponse);
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_CONFLICT) {
            throw new InvalidArgumentException('User already exists');
        }

        return $created;
    }

    public function update(string $email, User $user, ?string $appContext = null): ?User
    {
        $data = $this->serializer->normalize($user, null, ['write' => true]);
        $data = array_filter($data, static function ($v) {
            return $v !== null;
        });

        return $this->doUpdate($data, $email, $appContext);
    }

    public function patch(string $email, string $property, $value, ?string $appContext = null): ?User
    {
        $data = [$property => $value];

        return $this->doUpdate($data, $email, $appContext);
    }

    private function doUpdate($data, $email, $appContext): ?User
    {
        $params = array_filter([
            'email'               => $email,
            'application_context' => $appContext,
        ]);
        $apiResponse = $this->apiProvider->request('PATCH', '/api/users', [
            RequestOptions::QUERY       => $params,
            RequestOptions::JSON        => $data,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        $updated = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $updated = $this->deserialize($apiResponse);
        }

        return $updated;
    }

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
        }

        return $data;
    }

    public function delete(string $email): bool
    {
        $apiResponse = $this->apiProvider->request('DELETE', sprintf('/api/users/%s', $email), [
            RequestOptions::HTTP_ERRORS => false,
        ]);

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    public function sendActivationEmailAgain(string $email): bool
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PATCH, '/api/users/email/activation/' . $email, [
            RequestOptions::HTTP_ERRORS => false
        ]);

        $status = $apiResponse->getStatusCode();

        if ($status === Response::HTTP_CONFLICT) {
            throw new AccountAlreadyActivatedException();
        }

        if ($status === Response::HTTP_UNPROCESSABLE_ENTITY) {
            $this->lastError = (string) $apiResponse->getBody();
        }

        return $status === Response::HTTP_OK;
    }
}
