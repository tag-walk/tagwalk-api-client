<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Steve Valette <steve@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Psr\Log\LoggerInterface;
use Psr\Log\NullLogger;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class FilterManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var LoggerInterface
     */
    private $logger;

    /**
     * @param ApiProvider $apiProvider
     */
    public function __construct(ApiProvider $apiProvider)
    {
        $this->apiProvider = $apiProvider;
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
     * @param string|null $city
     * @param string|null $season
     * @param string|null $designers
     * @param string|null $individual
     * @param string|null $tags
     * @param string|null $language
     *
     * @return array
     */
    public function getStreetFilter(
        ?string $city,
        ?string $season,
        ?string $designers,
        ?string $individual,
        ?string $tags,
        ?string $language = null
    ): array {
        $data = [];
        $query = array_filter(compact('city', 'season', 'designers', 'individual', 'tags', 'language'));
        $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles/adaptive-filters', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $query,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode($apiResponse->getBody()->getContents(), true);
        } else {
            $this->logger->error('StreetstyleManager::adaptiveFiltersList unexpected status code', [
                'code'    => $apiResponse->getStatusCode(),
                'message' => $apiResponse->getBody()->getContents(),
            ]);
        }

        return $data;
    }
}
