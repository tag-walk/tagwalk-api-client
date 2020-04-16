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
     * @param array $query
     *
     * @return Press[]
     */
    public function list(array $query): array
    {
        $results = [];
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
        }

        return $results;
    }
}
