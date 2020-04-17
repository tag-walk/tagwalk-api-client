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
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Streetstyle;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\Status;

class StreetstyleManager
{
    /** @var int default listing size */
    public const DEFAULT_SIZE = 24;

    /**
     * @var int last list count
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
     * @param ApiProvider          $apiProvider
     * @param SerializerInterface  $serializer
     */
    public function __construct(
        ApiProvider $apiProvider,
        SerializerInterface $serializer
    ) {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param string $slug
     *
     * @return null|Streetstyle
     */
    public function get(string $slug): ?Streetstyle
    {
        $data = null;
        $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles/'.$slug, [RequestOptions::HTTP_ERRORS => false]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            /** @var Streetstyle $data */
            $data = $this->serializer->denormalize($data, Streetstyle::class);
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
                $data[$i] = $this->serializer->denormalize($datum, Streetstyle::class);
            }
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $data;
    }
}
