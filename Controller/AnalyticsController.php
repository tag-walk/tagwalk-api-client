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

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\BadRequestHttpException;
use Symfony\Component\Routing\Annotation\Route;
use Tagwalk\ApiClientBundle\Manager\AnalyticsManager;

/**
 * @Route("/analytics")
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
     * @Route("/media/{slug}", name="analytics_media", methods={"POST"}, options={"expose"=true})
     *
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function media(Request $request, string $slug): Response
    {
        if ($request->isXmlHttpRequest() === false) {
            throw new BadRequestHttpException();
        }
        $this->manager->media($slug, $request->request->all());

        return new Response();
    }

    /**
     * @Route("/streetstyle/{slug}", name="analytics_streetstyle", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param string $slug
     * @return Response
     */
    public function streetstyle(Request $request, string $slug): Response
    {
        if ($request->isXmlHttpRequest() === false) {
            throw new BadRequestHttpException();
        }
        $this->manager->streetstyle($slug, $request->request->all());

        return new Response();
    }

    /**
     * @Route("/page/{slug}", name="analytics_page", methods={"POST"}, options={"expose"=true})
     * @param Request $request
     * @param string $route
     * @return Response
     */
    public function page(Request $request, string $route): Response
    {
        if ($request->isXmlHttpRequest() === false) {
            throw new BadRequestHttpException();
        }
        $this->manager->page($route, $request->request->all());

        return new Response();
    }
}
