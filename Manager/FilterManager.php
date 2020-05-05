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
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class FilterManager
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @param ApiProvider $apiProvider
     */
    public function __construct(ApiProvider $apiProvider)
    {
        $this->apiProvider = $apiProvider;
    }

    /**
     * $params['city']          = (string) The city selected to restrict the results (Required false)
     * $params['season']        = (string) The season selected to restrict the results (Required false)
     * $params['designers']     = (string) A comma-seperated string of designers seleted to restrict the results
     *                            (Required false)
     * $params['individual']    = (string) The individual selected to restrict the results (Required false)
     * $params['tags']          = (string) A comma-seperated string of tags to restrict the results (Required false)
     * $params['language']      = (string) Locale language.
     *
     * @param array $params
     *
     * @return array
     */
    public function getStreetFilter(array $params): array
    {
        $data = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/streetstyles/adaptive-filters', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $params,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
        }

        return $data;
    }

    /**
     * $params['city']          = (string) The city selected to restrict the results (Required false)
     * $params['season']        = (string) The season selected to restrict the results (Required false)
     * $params['designers']     = (string) A comma-seperated string of designers seleted to restrict the results
     *                            (Required false)
     * $params['model']         = (string) The individual selected to restrict the results (Required false)
     * $params['tags']          = (string) A comma-seperated string of tags to restrict the results (Required false)
     * $params['category']      = (string) The accesory_category selected to restrict the results (Required false)
     * $params['includes']      = (string) The comma-seperated (designer, city, season, models, category, tags)
     *                            allow multiple values (Require true)
     * $params['language']      = (string) Locale language.
     *
     * @param array $params
     *
     * @return array
     */
    public function getLookFilter(?array $params): array
    {
        $data = [];
        $apiResponse = $this->apiProvider->request('GET', '/api/medias/adaptive-filters', [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $params,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $data = json_decode((string) $apiResponse->getBody(), true);
        }

        return $data;
    }
}
