<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2019 TAGWALK
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
use Tagwalk\ApiClientBundle\Exception\ApiAccessDeniedException;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ShowroomUserManager
{
    public const DEFAULT_STATUS = 'all';

    public const DEFAULT_SIZE = 10;

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
     * @var int last query result count
     */
    public $lastCount;

    /**
     * @param ApiProvider          $apiProvider
     * @param SerializerInterface  $serializer
     * @param LoggerInterface|null $logger
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer,
        ?LoggerInterface $logger = null
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
        $this->logger = $logger ?? new NullLogger();
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
            $response->getBody()->getContents(),
            User::class,
            JsonEncoder::FORMAT
        );

        return $user;
    }

    /**
     * @param User $user
     *
     * @return User|null
     */
    public function create(User $user): ?User
    {
        $data = $this->serializer->normalize($user);
        $data = array_filter($data, static function ($v) {
            return $v !== null;
        });
        $apiResponse = $this->apiProvider->request('POST', '/api/showroom/users/register', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $data,
        ]);
        $created = null;
        switch ($apiResponse->getStatusCode()) {
            case Response::HTTP_FORBIDDEN:
                throw new ApiAccessDeniedException();
            case Response::HTTP_CREATED:
                $created = $this->deserialize($apiResponse);
                break;
            case Response::HTTP_CONFLICT:
                $this->logger->notice(
                    'ShowroomUser already exists',
                    [
                        'code'    => $apiResponse->getStatusCode(),
                        'message' => $apiResponse->getBody()->getContents(),
                    ]
                );
                break;
            default:
                $this->logger->error(
                    'ShowroomUserManager::create unexpected status code',
                    [
                        'code'    => $apiResponse->getStatusCode(),
                        'message' => $apiResponse->getBody()->getContents(),
                    ]
                );
        }

        return $created;
    }

    /**
     * @param string $email
     *
     * @return User|null
     */
    public function createSuperManager(string $email): ?User
    {
        $apiResponse = $this->apiProvider->request('POST', '/api/showroom/users/register/super-manager', [
            RequestOptions::HTTP_ERRORS  => false,
            RequestOptions::QUERY        => ['email' => $email],
        ]);
        $created = null;
        switch ($apiResponse->getStatusCode()) {
            case Response::HTTP_FORBIDDEN:
                throw new ApiAccessDeniedException();
            case Response::HTTP_CREATED:
                $created = $this->deserialize($apiResponse);
                break;
            case Response::HTTP_CONFLICT:
                $this->logger->notice(
                    'Super manager already exists',
                    [
                        'code'    => $apiResponse->getStatusCode(),
                        'message' => $apiResponse->getBody()->getContents(),
                    ]
                );
                break;
            default:
                $this->logger->error(
                    'ShowroomUserManager::createSuperManager unexpected status code',
                    [
                        'code'    => $apiResponse->getStatusCode(),
                        'message' => $apiResponse->getBody()->getContents(),
                    ]
                );
        }

        return $created;
    }

    /**
     * @param string $status
     * @param int    $from
     * @param int    $size
     *
     * @return array
     */
    public function list(string $status = self::DEFAULT_STATUS, int $from = 0, int $size = 10): array
    {
        $result = [];
        $this->lastCount = 0;
        $query = array_filter(compact('from', 'size', 'status'));
        $apiResponse = $this->apiProvider->request('GET', '/api/showroom/users', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody()->getContents(), true);
            foreach ($data as $i => $datum) {
                $result[$i] = $this->serializer->denormalize($datum, User::class);
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } else {
            $this->logger->error('ShowroomUserManager::list unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $result;
    }
}
