<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2018 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\Routing\Annotation\Route;
use Tagwalk\ApiClientBundle\Manager\AnalyticsManager;

/**
 * Tagwalk tracking system.
 *
 * @Route("/tts")
 */
class AnalyticsController extends AbstractController
{
    /**
     * @var AnalyticsManager
     */
    private $manager;

    /**
     * @param AnalyticsManager $manager
     */
    public function __construct(AnalyticsManager $manager)
    {
        $this->manager = $manager;
    }

    /**
     * @Route("/media/{slug}", name="tts_media", methods={"POST"}, options={"expose"=true})
     *
     * @param Request $request
     * @param string  $slug
     *
     * @return Response
     */
    public function media(Request $request, string $slug): Response
    {
        $this->manager->media($slug, $request->request->all(), $request->getClientIp());

        return new Response();
    }

    /**
     * @Route("/streetstyle/{slug}", name="tts_streetstyle", methods={"POST"}, options={"expose"=true})
     *
     * @param Request $request
     * @param string  $slug
     *
     * @return Response
     */
    public function streetstyle(Request $request, string $slug): Response
    {
        $this->manager->streetstyle($slug, $request->request->all(), $request->getClientIp());

        return new Response();
    }

    /**
     * @Route("/page/{route}", name="tts_page", methods={"POST"}, options={"expose"=true})
     *
     * @param Request $request
     * @param string  $route
     *
     * @return Response
     */
    public function page(Request $request, string $route): Response
    {
        $this->manager->page($request, $route, $request->request->all());

        return new Response();
    }

    /**
     * @Route("/photos/{route}/{event}", name="tts_photos", methods={"POST"}, options={"expose"=true})
     *
     * @param Request $request
     * @param string  $route
     * @param string  $event
     *
     * @return Response
     */
    public function photos(Request $request, string $route, string $event): Response
    {
        $this->manager->photos($request, $route, $event, $request->request->all(), $request->getClientIp());

        return new Response();
    }

    /**
     * @Route("/outbound", name="tts_outbound", methods={"POST"}, options={"expose"=true})
     *
     * @param Request $request
     *
     * @return Response
     */
    public function outbound(Request $request): Response
    {
        $this->manager->outbound($request->request->all(), $request->getClientIp());

        return new Response();
    }
}
