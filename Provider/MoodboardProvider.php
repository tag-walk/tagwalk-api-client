<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Vincent Duruflé <vincent@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Provider;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

class MoodboardProvider
{
    /**
     * @var ApiProvider
     */
    private $apiProvider;

    /**
     * @var int
     */
    public $lastCount;

    public function __construct(
        ApiProvider $apiProvider
    ) {
        $this->apiProvider = $apiProvider;
    }

    /**
     * @param string     $slug
     * @param array|null $params
     *
     * @return null|array
     */
    public function get(string $slug, $params = []): ?array
    {
        $moodboard = null;
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, '/api/moodboards/'.$slug, [
            RequestOptions::HTTP_ERRORS => false,
            RequestOptions::QUERY       => $params,
        ]);
        if ($apiResponse->getStatusCode() === Response::HTTP_OK) {
            $moodboard = json_decode((string) $apiResponse->getBody(), true);
            $this->lastCount = (int) $apiResponse->getHeaderLine('X-Total-Count');
        }

        return $moodboard;
    }
}
