<?php

/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Vincent Duruflé <vincent@tag-walk.com>
 * @copyright   2020 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class ShowroomManager
{
    private ApiProvider $apiProvider;

    public function __construct(ApiProvider $apiProvider)
    {
        $this->apiProvider = $apiProvider;
    }

    public function list()
    {
        $showrooms = null;

        $apiResponse = $this->apiProvider->request('GET', '/api/admin/showroom');

        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $showrooms = json_decode((string) $apiResponse->getBody(), true);
        }

        return $showrooms;
    }
}
