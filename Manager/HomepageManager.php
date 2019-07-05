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
use InvalidArgumentException;
use Psr\Log\LoggerInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;
use Tagwalk\ApiClientBundle\Utils\Constants\HomepageSection;

class HomepageManager
{
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
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param LoggerInterface $logger
     */
    public function setLogger(LoggerInterface $logger): void
    {
        $this->logger = $logger;
    }

    /**
     * @param string      $section
     * @param null|string $language
     *
     * @return Homepage|null
     */
    public function getBySection(string $section, ?string $language = null): ?Homepage
    {
        if (false === in_array($section, HomepageSection::VALUES, true)) {
            throw new InvalidArgumentException('Invalid homepage section argument');
        }
        $record = null;
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            "/api/homepages/show/{$section}",
            [
                RequestOptions::QUERY       => ['language' => $language],
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Homepage::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('HomepageManager::getBySection invalid status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $record;
    }

    /**
     * @param string      $slug
     * @param null|string $language
     *
     * @return Homepage|null
     */
    public function get(string $slug, ?string $language = null): ?Homepage
    {
        $record = null;
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            "/api/homepages/{$slug}",
            [
                RequestOptions::QUERY       => ['language' => $language],
                RequestOptions::HTTP_ERRORS => false,
            ]
        );
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $record = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Homepage::class, JsonEncoder::FORMAT);
        } elseif ($apiResponse->getStatusCode() !== Response::HTTP_NOT_FOUND) {
            $this->logger->error('HomepageManager::get invalid status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $record;
    }
}
