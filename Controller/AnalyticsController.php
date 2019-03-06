<?php
/**
 * PHP version 7
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Controller;

use Psr\Log\LoggerInterface;
use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

class AnalyticsController extends AbstractController
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
     * @param LoggerInterface $logger
     */
    public function __construct(ApiProvider $apiProvider, LoggerInterface $logger)
    {
        $this->apiProvider = $apiProvider;
        $this->logger = $logger;
    }

    /**
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function addMediaView(Request $request, string $slug): Response
    {
        $response = $this->apiProvider->request('POST', "/api/analytics/media/$slug", ['query' => $request->query->all()]);
        if ($response->getStatusCode() !== Response::HTTP_CREATED) {
            $this->logger->critical('Analytics API error', $response->getBody()->getContents());
        }

        return new Response('', $response->getStatusCode());
    }

    /**
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function addStreetstyleView(Request $request, string $slug): Response
    {
        $response = $this->apiProvider->request('POST', "/api/analytics/streetstyle/$slug", ['query' => $request->query->all()]);
        if ($response->getStatusCode() !== Response::HTTP_CREATED) {
            $this->logger->critical('Analytics API error', $response->getBody()->getContents());
        }

        return new Response('', $response->getStatusCode());
    }
}