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
     * @param ApiProvider          $apiProvider
     * @param LoggerInterface|null $logger
     */
    public function __construct(ApiProvider $apiProvider, LoggerInterface $logger = null)
    {
        $this->apiProvider = $apiProvider;
        $this->logger = $logger ?? new NullLogger();
    }

    /**
     * $params['city']          = (string) The city selected to restrict the results (Required false)
     * $params['season']        = (string) The season selected to restrict the results (Required false)
     * $params['designers']     = (string) The designers seleted to restrict the results (Required false)
     * $params['individual']    = (string) The individual selected to restrict the results (Required false)
     * $params['tags']          = (string)   A comma-seperated string of tags to restrict the results (Required false)
     * $params['language']      = (string) locale language.
     *
     * @param array $params
     *
     * @return array
     */
    public function getStreetFilter(array $params): array
    {
        $data = [];
        $query = array_combine(array_keys($params), array_map(static function ($v) {
            return $v;
        }, $params));
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
