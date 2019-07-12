<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Seller;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class SellerManager
{
    public const DEFAULT_SORT = 'name:asc';

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
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
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    ) {
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
     * @param string      $slug
     * @param null|string $language
     *
     * @return Seller
     */
    public function get(string $slug, ?string $language = null): ?Seller
    {
        $record = null;
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            "/api/sellers/{$slug}",
            [
                RequestOptions::QUERY       => ['language' => $language],
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Seller::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('SellerManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $record;
    }

    /**
     * @param null|string $language
     * @param int|null    $from
     * @param int|null    $size
     * @param null|string $sort
     * @param null|string $status
     *
     * @return Seller[]
     */
    public function list(
        ?string $language = null,
        ?int $from = 0,
        ?int $size = 100,
        ?string $sort = self::DEFAULT_SORT,
        ?string $status = Status::ENABLED
    ): array {
        $records = [];
        $query = compact('from', 'size', 'language', 'sort', 'status');
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            '/api/sellers',
            [
                RequestOptions::QUERY       => $query,
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody()->getContents(), true);
            foreach ($data as $datum) {
                $records[] = $this->serializer->denormalize($datum, Seller::class);
            }
        } else {
            $this->logger->error('SellerManager::list unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $records;
    }
}
