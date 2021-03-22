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
use Tagwalk\ApiClientBundle\Manager\EventManager;
use Tagwalk\ApiClientBundle\Manager\IndividualManager;
use Tagwalk\ApiClientBundle\Manager\TagManager;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

/**
 * @Route("/autocomplete", options={"expose"=true})
 */
class AutocompleteController extends AbstractController
{
    protected ApiProvider $apiProvider;
    private DesignerManager $designerManager;
    private IndividualManager $individualManager;
    private TagManager $tagManager;
    private EventManager $eventManager;

    public function __construct(
        ApiProvider $apiProvider,
        DesignerManager $designerManager,
        IndividualManager $individualManager,
        TagManager $tagManager,
        EventManager $eventManager
    ) {
        $this->apiProvider = $apiProvider;
        $this->designerManager = $designerManager;
        $this->individualManager = $individualManager;
        $this->tagManager = $tagManager;
        $this->eventManager = $eventManager;
    }

    /**
     * @Route("/designer", name="autocomplete_designer")
     */
    public function designer(Request $request): JsonResponse
    {
        $search = $request->query->get('search');
        $status = $request->query->get('status');

        if (false === empty($search)) {
            $results = $this->designerManager->suggest($search, $request->getLocale());
            $count = min(10, count($results));
        } else {
            $page = $request->query->get('page', 1);
            $results = $this->designerManager->list(
                $request->getLocale(),
                ($page - 1) * 20,
                20,
                DesignerManager::DEFAULT_SORT,
                $status ?? DesignerManager::DEFAULT_STATUS,
                false,
                false,
                false
            );
            $count = $this->designerManager->lastQueryCount;
        }

        $response = new JsonResponse([
            'results'     => $results,
            'total_count' => $count,
        ]);

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
        $language = $request->query->get('language');
        if (false === empty($search)) {
            $results = $this->tagManager->suggest($search, $language);
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
     * @Route("/individual", name="autocomplete_individual")
     */
    public function individual(Request $request): JsonResponse
    {
        $results = [];
        $search = $request->query->get('search');

        if (false === empty($search)) {
            $results = $this->individualManager->autocomplete($search);
        }

        $data = [
            'results'     => $results,
            'total_count' => count($results),
        ];

        $response = new JsonResponse($data);
        $response->setCache([
            'max_age'  => 300,
            's_maxage' => 300,
            'public'   => true,
        ]);

        return $response;
    }

    /**
     * @Route("/event", name="autocomplete_event")
     */
    public function event(Request $request): JsonResponse
    {
        $results = [];
        $search = $request->query->get('search');

        if (false === empty($search)) {
            $results = $this->eventManager->autocomplete($search);
        }

        $data = [
            'results'     => $results,
            'total_count' => count($results),
        ];

        $response = new JsonResponse($data);
        $response->setCache([
            'max_age'  => 300,
            's_maxage' => 300,
            'public'   => true,
        ]);

        return $response;
    }
}
