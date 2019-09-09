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
use Tagwalk\ApiClientBundle\Model\ShowroomUser;
use Tagwalk\ApiClientBundle\Model\User;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ShowroomUserManager
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
     * @param ResponseInterface $response
     *
     * @return ShowroomUser
     */
    private function deserialize($response): ShowroomUser
    {
        return $this->serializer->deserialize(
            $response->getBody()->getContents(),
            ShowroomUser::class,
            JsonEncoder::FORMAT
        );
    }

    /**
     * @param ShowroomUser $user
     *
     * @return ShowroomUser|null
     */
    public function create(ShowroomUser $user): ?ShowroomUser
    {
		$data = $this->serializer->normalize($user);
		$data = array_filter($data, static function ($v) {
			return $v !== null;
		});
		$data = $this->serializer->normalize($data, null, ['groups' => 'showroom_user']);
		$apiResponse = $this->apiProvider->request('POST', '/api/showroom/users/register', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON        => $data,
        ]);
        $created = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $created = $this->deserialize($apiResponse);
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_CONFLICT) {
            $this->logger->notice('User already exists', [
               'code'    => $apiResponse->getStatusCode(),
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
}
