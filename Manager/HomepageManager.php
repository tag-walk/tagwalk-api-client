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

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Serializer\Serializer;
use Symfony\Component\Serializer\SerializerInterface;
use Tagwalk\ApiClientBundle\Model\Cover;
use Tagwalk\ApiClientBundle\Model\Homepage;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

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
     * @param ApiProvider $apiProvider
     * @param SerializerInterface $serializer
     */
    public function __construct(ApiProvider $apiProvider, SerializerInterface $serializer)
    {
        $this->apiProvider = $apiProvider;
        $this->serializer = $serializer;
    }

    /**
     * @param string $section
     *
     * @return Cover
     */
    public function getBySection(string $section)
    {
        if (false === in_array($section, Homepage::SECTIONS)) {
            throw new \InvalidArgumentException('Invalid homepage section argument');
        }
        $apiResponse = $this->apiProvider->request(Request::METHOD_GET, "/api/homepages/show/{$section}");
        $homepage = $this->serializer->deserialize($apiResponse->getBody()->getContents(), Homepage::class, 'json');

        return $homepage;
    }
}
