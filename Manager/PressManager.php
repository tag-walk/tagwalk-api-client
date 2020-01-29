<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2019 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use OutOfBoundsException;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Press;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class PressManager
{
    /**
     * @var int last query result count
     */
    public $lastCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var SerializerInterface
     */
    private $serializer;

    /**
     * @var LoggerInterface
     */
    private $logger;

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
     * @param array $query
     *
     * @return Press[]
     */
    public function list(array $query): array
    {
        $results = [];
        $this->lastCount = 0;
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/press', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody()->getContents(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, Press::class);
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE) {
            throw new OutOfBoundsException('API response: Range not satisfiable');
        } else {
            $this->logger->error('PressManager::list unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $results;
    }
}
