<?php
/**
 * PHP version 7
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
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Serializer\Normalizer\StreetstyleNormalizer;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class StreetstyleManager
{
    /** @var int default listing size */
    public const DEFAULT_SIZE = 12;

    /**
     * @var int last list count
     */
    public $lastCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var StreetstyleNormalizer
     */
    private $streetstyleNormalizer;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider           $apiProvider
     * @param StreetstyleNormalizer $streetstyleNormalizer
     */
    public function __construct(ApiProvider $apiProvider, StreetstyleNormalizer $streetstyleNormalizer)
    {
        $this->apiProvider = $apiProvider;
        $this->streetstyleNormalizer = $streetstyleNormalizer;
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
     * @param string $slug
     *
     * @return null|Streetstyle
     */
    public function get(string $slug): ?Streetstyle
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles/' . $slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            $data = $this->streetstyleNormalizer->denormalize($data, Streetstyle::class);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('StreetstyleManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }

    /**
     * @param array  $query
     * @param int    $from
     * @param int    $size
     * @param string $status
     *
     * @return array
     */
    public function list($query = [], $from = 0, $size = self::DEFAULT_SIZE, $status = Status::ENABLED): array
    {
        $data = [];
        $this->lastCount = 0;
        $query = array_merge($query, compact('from', 'size', 'status'));
        $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            foreach ($data as $i => $datum) {
                $data[$i] = $this->streetstyleNormalizer->denormalize($datum, Streetstyle::class);
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        } elseif ($apiResponse->getStatusCode() === Response::HTTP_REQUESTED_RANGE_NOT_SATISFIABLE) {
            throw new OutOfBoundsException('API response: Range not satisfiable');
        } else {
            $this->logger->error('StreetstyleManager::get unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }
}
