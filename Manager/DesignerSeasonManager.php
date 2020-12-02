<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author    Thomas Barriac <thomas@tag-walk.com>
 * @copyright 2020 TAGWALK
 * @license   proprietary
 */

namespace Tagwalk\ApiClientBundle\Manager;

use GuzzleHttp\RequestOptions;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Serializer\Encoder\JsonEncoder;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\DesignerSeason;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class DesignerSeasonManager
{
    private ApiProvider $apiProvider;
    private Serializer $serializer;

    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    public function get(string $slug, ?string $language = null): ?DesignerSeason
    {
        $apiResponse = $this->apiProvider->request(
            Request::METHOD_GET,
            sprintf('/api/designers/seasons/%s', $slug),
            [
                RequestOptions::HTTP_ERRORS => false,
                RequestOptions::QUERY => array_filter(['language' => $language]),
            ]
        );

        if ($apiResponse->getStatusCode() !== Response::HTTP_OK) {
            return null;
        }

        /** @var DesignerSeason $data */
        $data = $this->serializer->deserialize($apiResponse->getBody(), DesignerSeason::class, JsonEncoder::FORMAT);

        return $data;
    }

    public function update(DesignerSeason $designerSeason): bool
    {
        $slug = sprintf('%s_%s', $designerSeason->getDesigner(), $designerSeason->getSeason());
        $apiResponse = $this->apiProvider->request(Request::METHOD_POST,
            sprintf('/api/designers/seasons/%s', $slug),
            [
                RequestOptions::JSON => $this->serializer->normalize($designerSeason, null, ['write' => true])
            ]
        );

        return $apiResponse->getStatusCode() === Response::HTTP_OK;
    }
}
