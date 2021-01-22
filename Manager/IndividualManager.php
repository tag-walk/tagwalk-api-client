<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Exception\SlugNotAvailableException;
use Tagwalk\ApiClientBundle\Model\Individual;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class IndividualManager
{
    public const DEFAULT_STATUS = 'enabled';
    public const DEFAULT_SORT = 'name:asc';
    public const DEFAULT_MODEL = 'true';

    protected ApiProvider $apiProvider;

    /** @var Serializer */
    protected $serializer;

    /**
     * @var int
     */
    public $lastCount;

    private array $lastErrors = [];

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function get(string $slug, ?string $language = null): ?Individual
    {
        $individual = null;
        $query = array_filter(compact('language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/'.$slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            /** @var Individual $individual */
            $individual = $this->serializer->deserialize(
                (string) $apiResponse->getBody(),
                Individual::class,
                JsonEncoder::FORMAT
            );
        }

        return $individual;
    }

    /**
     * @return array|Individual[]
     */
    public function list(
        string $language = null,
        int $from = 0,
        int $size = 20,
        string $sort = self::DEFAULT_SORT,
        string $status = self::DEFAULT_STATUS,
        bool $denormalize = true,
        string $model = self::DEFAULT_MODEL
    ): array {
        $individuals = [];
        $this->lastCount = 0;
        $query = array_filter(compact('from', 'size', 'sort', 'status', 'language', 'model'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals', [
            RequestOptions::QUERY       => $query,
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);

            if ($denormalize) {
                foreach ($data as $datum) {
                    $individuals[] = $this->serializer->denormalize($datum, Individual::class);
                }
            } else {
                $individuals = $data;
            }

            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $individuals;
    }

    public function count(string $status = self::DEFAULT_STATUS): int
    {
        $count = 0;
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals', [
            RequestOptions::QUERY       => [
                'status' => $status,
                'size'   => 1,
            ],
            RequestOptions::HTTP_ERRORS => false,
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $count = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $count;
    }

    public function suggest(string $prefix, string $language = null): array
    {
        $individuals = [];
        $query = array_filter(compact('prefix', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/suggestions', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $individuals = json_decode((string) $apiResponse->getBody(), true);
        }

        return $individuals;
    }

    /**
     * @return Individual[]
     */
    public function listFiltersStreet(
        ?string $city,
        ?string $season,
        ?string $designers,
        ?string $tags,
        ?string $language = null
    ): array {
        $results = [];
        $query = array_filter(compact('city', 'season', 'designers', 'tags', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/individuals/filter/streetstyle', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
            foreach ($data as $datum) {
                $results[] = $this->serializer->denormalize($datum, Individual::class);
            }
        }

        return $results;
    }

    /**
     * @throws SlugNotAvailableException When an individual with the same slug already exists
     */
    public function create(Individual $individual): ?Individual
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_POST, '/api/individuals', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => $this->serializer->normalize($individual, null, ['write' => true])
        ]);

        if ($apiResponse->getStatusCode() === Response::HTTP_CONFLICT) {
            throw new SlugNotAvailableException();
        }

        $data = json_decode($apiResponse->getBody(), true);

        if ($apiResponse->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            $this->lastErrors = $data['errors'];

            return null;
        }

        if ($apiResponse->getStatusCode() !== Response::HTTP_CREATED) {
            return null;
        }

        return $this->serializer->denormalize($data, Individual::class);
    }

    public function update(Individual $individual): ?Individual
    {
        $apiResponse = $this->apiProvider->request(Request::METHOD_PUT, '/api/individuals/' . $individual->getSlug(), [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::JSON => $this->serializer->normalize($individual, null, ['write' => true])
        ]);

        $data = json_decode($apiResponse->getBody(), true);

        if ($apiResponse->getStatusCode() === Response::HTTP_BAD_REQUEST) {
            $this->lastErrors = $data['errors'];

            return null;
        }

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        return $this->serializer->denormalize($data, Individual::class);
    }

    public function toggleStatus(string $slug): bool
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_PATCH,
            sprintf('/api/individuals/%s/status', $slug),
            [RequestOptions::HTTP_ERRORS => false]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }

    public function getLastErrors(): array
    {
        $errors = $this->lastErrors;
        $this->lastErrors = [];

        return $errors;
    }
}
