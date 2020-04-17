<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Offer;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class OfferManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'position:asc';
    public const DEFAULT_SIZE = 10;

    /**
     * @var int|null
     */
    public $lastCount;

    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var Serializer
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
     * @param int         $from
     * @param int         $size
     * @param string      $sort
     * @param string      $status
     * @param null|string $name
     * @param null|string $text
     *
     * @return Offer[]
     */
    public function list(
        int $from = 0,
        int $size = self::DEFAULT_SIZE,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        ?string $name = null,
        ?string $text = null
    ): array {
        $offers = [];
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'name', 'text'));
        $apiResponse = $this->apiProvider->request('GET', '/api/offers', [RequestOptions::QUERY => $query]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
            $data = json_decode($apiResponse->getBody(), true);
            $offers = [];
            foreach ($data as $datum) {
                $offers[] = $this->serializer->denormalize($datum, Offer::class);
            }
        }

        return $offers;
    }

    /**
     * @param string      $slug
     * @param null|string $language
     *
     * @return Offer|null
     */
    public function get(string $slug, ?string $language = null): ?Offer
    {
        $offer = null;
        $query = compact('language');
        $apiResponse = $this->apiProvider->request(
            'GET',
            '/api/offers/'.$slug,
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY       => $query,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            /** @var Offer $offer */
            $offer = $this->serializer->denormalize($data, Offer::class);
        }

        return $offer;
    }

    /**
     * @param string $slug
     *
     * @return bool
     */
    public function delete(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request(
            'DELETE',
            '/api/offers/'.$slug,
            [
                RequestOptions::HTTP_ERRORS => false,
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_NO_CONTENT;
    }

    /**
     * @param Offer $record
     *
     * @return Offer|null
     */
    public function create(Offer $record): ?Offer
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($record, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('POST', '/api/offers', $params);
        $data = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_CREATED) {
            $json = json_decode($apiResponse->getBody(), true);
            /** @var Offer $data */
            $data = $this->serializer->denormalize($json, Offer::class);
        }

        return $data;
    }

    /**
     * @param string $slug
     * @param Offer  $record
     *
     * @return Offer
     */
    public function update(string $slug, Offer $record): ?Offer
    {
        $params = [RequestOptions::JSON => $this->serializer->normalize($record, null, ['write' => true])];
        $apiResponse = $this->apiProvider->request('PUT', '/api/offers/'.$slug, $params);
        $updated = null;
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody(), true);
            /** @var Offer $updated */
            $updated = $this->serializer->denormalize($data, Offer::class);
        }

        return $updated;
    }
}
