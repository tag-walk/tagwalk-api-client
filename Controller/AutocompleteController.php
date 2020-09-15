<?php
/**
 * PHP version 7.
 *
 * LICENSE: This source file is subject to copyright
 *
 * @author      Florian Ajir <florian@tag-walk.com>
 * @copyright   2016-2019 TAGWALK
 * @license     proprietary
 */

namespace Tagwalk\ApiClientBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\AbstractController;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tagwalk\ApiClientBundle\Manager\DesignerManager;
use Tagwalk\ApiClientBundle\Manager\IndividualManager;
use Tagwalk\ApiClientBundle\Manager\TagManager;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

/**
 * @Route("/autocomplete")
 */
class AutocompleteController extends AbstractController
{
    /**
     * @var ApiProvider
     */
    protected $apiProvider;
    /**
     * @var DesignerManager
     */
    private $designerManager;
    /**
     * @var IndividualManager
     */
    private $individualManager;
    /**
     * @var TagManager
     */
    private $tagManager;

    /**
     * @param ApiProvider       $apiProvider
     * @param DesignerManager   $designerManager
     * @param IndividualManager $individualManager
     * @param TagManager        $tagManager
     */
    public function __construct(ApiProvider $apiProvider, DesignerManager $designerManager, IndividualManager $individualManager, TagManager $tagManager)
    {
        $this->apiProvider = $apiProvider;
        $this->designerManager = $designerManager;
        $this->individualManager = $individualManager;
        $this->tagManager = $tagManager;
    }

    /**
     * @Route("/designer", name="autocomplete_designer")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function designer(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        if (false === empty($search)) {
            $results = $this->designerManager->suggest($search, $request->getLocale());
            $count = min(10, count($results));
        } else {
            $page = $request->query->get('page', 1);
            $results = $this->designerManager->list(
                $request->getLocale(),
                ($page - 1) * 20,
                20,
                $this->designerManager::DEFAULT_SORT,
                $this->designerManager::DEFAULT_STATUS,
                false,
                false,
                false
            );
            $count = $this->designerManager->lastQueryCount;
        }
        $data = [
            'results'     => $results,
            'total_count' => $count,
        ];
        $response = new JsonResponse($data);
        $response->setCache([
            'max_age'  => 600,
            's_maxage' => 600,
            'public'   => true,
        ]);

        return $response;
    }

    /**
     * @Route("/tag", name="autocomplete_tag")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function tag(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        if (false === empty($search)) {
            $results = $this->tagManager->suggest($search, $request->getLocale());
            $count = min(10, count($results));
        } else {
            $page = $request->query->get('page', 1);
            $results = $this->tagManager->list(
                $request->getLocale(),
                ($page - 1) * 20,
                20,
                $request->getLocale() === 'en' ? 'name:asc' : 'name_'.$request->getLocale().':asc',
                $this->tagManager::DEFAULT_STATUS,
                false
            );
            $count = $this->tagManager->lastCount;
        }
        $data = [
            'results'     => $results,
            'total_count' => $count,
        ];
        $response = new JsonResponse($data);
        $response->setCache([
            'max_age'  => 600,
            's_maxage' => 600,
            'public'   => true,
        ]);

        return $response;
    }

    /**
     * @Route("/tag/nearby", name="nearby-tags")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function nearbyTags(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        $results = [];
        if (!empty($search)) {
            $results = $this->tagManager->nearby($search, $request->getLocale());
        }
        $response = new JsonResponse($results);
        $response->setCache([
            'max_age'  => 600,
            's_maxage' => 600,
            'public'   => true,
        ]);

        return $response;
    }

    /**
     * @Route("/individual", name="autocomplete_individual")
     *
     * @param Request $request
     *
     * @return JsonResponse
     */
    public function individual(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        if (false === empty($search)) {
            $results = $this->individualManager->suggest($search, $request->getLocale());
            $count = min(10, count($results));
        } else {
            $page = $request->query->get('page', 1);
            $results = $this->individualManager->list(
                $request->getLocale(),
                ($page - 1) * 20,
                20,
                $this->individualManager::DEFAULT_SORT,
                $this->individualManager::DEFAULT_STATUS,
                false
            );
            $count = $this->individualManager->lastCount;
        }
        $data = [
            'results'     => $results,
            'total_count' => $count,
        ];
        $response = new JsonResponse($data);
        $response->setCache([
            'max_age'  => 86400,
            's_maxage' => 86400,
            'public'   => true,
        ]);

        return $response;
    }
}
