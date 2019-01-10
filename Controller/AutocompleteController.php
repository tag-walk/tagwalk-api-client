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
use Symfony\Component\Cache\Simple\FilesystemCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Annotation\Route;
use Tagwalk\ApiClientBundle\Provider\ApiProvider;

/**
 * @Route("/api/autocomplete")
 */
class AutocompleteController extends AbstractController
{
    /**
     * @var ApiProvider
     */
    protected $apiProvider;

    /**
     * @param ApiProvider $apiProvider
     */
    public function __construct(ApiProvider $apiProvider)
    {
        $this->apiProvider = $apiProvider;
    }

    /**
     * @Route("/designer", name="autocomplete_designer")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function designer(Request $request)
    {
        $search = $request->query->get('search');
        if (false === empty($search)) {
            $cache = new FilesystemCache('autocomplete.designer', 3600);
            if ($cache->has($search)) {
                $results = $cache->get($search);
            } else {
                $query = [
                    'prefix' => $search,
                    'language' => $request->getLocale()
                ];
                $apiResponse = $this->apiProvider->request('GET', '/api/designers/suggestions', ['query' => $query, 'http_errors' => false]);
                $results = json_decode($apiResponse->getBody(), true);
                $cache->set($search, $results);
            }
            $count = 10;
        } else {
            $page = $request->query->get('page', 1);
            $cache = new FilesystemCache('designer.list.page', 3600);
            if ($cache->has($page) && $cache->has($page . '.count')) {
                $results = $cache->get($page);
                $count = $cache->get($page . '.count');
            } else {
                $query = [
                    'from' => ($page - 1) * 10,
                    'size' => 10,
                    'sort' => 'name:asc',
                    'status' => 'enabled',
                    'language' => $request->getLocale()
                ];
                $apiResponse = $this->apiProvider->request('GET', '/api/designers', ['query' => $query, 'http_errors' => false]);
                $results = json_decode($apiResponse->getBody(), true);
                $count = (int)$apiResponse->getHeader('X-Total-Count')[0];
                $cache->set($page, $results);
                $cache->set($page . '.count', $count);
            }
        }
        $data = [
            'results' => $results,
            'total_count' => $count
        ];

        $response = new JsonResponse($data);
        $response->setCache([
            'max_age' => 3600,
            's_maxage' => 3600,
            'public' => true
        ]);

        return $response;
    }

    /**
     * @Route("/tag", name="autocomplete_tag")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function tag(Request $request)
    {
        $search = $request->query->get('search');
        $nameKey = $request->getLocale() === 'en' ? 'name' : 'name_' . $request->getLocale();
        if (false === empty($search)) {
            $cache = new FilesystemCache('autocomplete.tag', 3600);
            if ($cache->has($search)) {
                $results = $cache->get($search);
            } else {
                $query = [
                    'prefix' => $search,
                    'language' => $request->getLocale()
                ];
                $apiResponse = $this->apiProvider->request('GET', '/api/tags/suggestions', ['query' => $query, 'http_errors' => false]);
                $results = json_decode($apiResponse->getBody(), true);
                $cache->set($search, $results);
            }
            $count = 10;
        } else {
            $page = $request->query->get('page', 1);
            $cache = new FilesystemCache('tag.list.page', 3600);
            if ($cache->has($page) && $cache->has($page . '.count')) {
                $results = $cache->get($page);
                $count = $cache->get($page . '.count');
            } else {
                $query = [
                    'from' => ($page - 1) * 10,
                    'size' => 10,
                    'sort' => $nameKey . ':asc',
                    'status' => 'enabled',
                    'language' => $request->getLocale()
                ];
                $apiResponse = $this->apiProvider->request('GET', '/api/tags', ['query' => $query, 'http_errors' => false]);
                $results = json_decode($apiResponse->getBody(), true);
                $count = (int)$apiResponse->getHeader('X-Total-Count')[0];
                $cache->set($page, $results);
                $cache->set($page . '.count', $count);
            }
        }
        foreach ($results as &$result) {
            $result = [
                'slug' => $result['slug'],
                'name' => $result['name']
            ];
        }

        $data = [
            'results' => $results,
            'total_count' => $count
        ];

        $response = new JsonResponse($data);
        $response->setCache([
            'max_age' => 3600,
            's_maxage' => 3600,
            'public' => true
        ]);

        return $response;
    }

    /**
     * @Route("/model", name="autocomplete_model")
     *
     * @param Request $request
     *
     * @return JsonResponse
     * @throws \GuzzleHttp\Exception\GuzzleException
     * @throws \Psr\SimpleCache\InvalidArgumentException
     */
    public function individuals(Request $request)
    {
        $search = $request->query->get('search');
        if (false === empty($search)) {
            $cache = new FilesystemCache('autocomplete.individual', 3600);
            if ($cache->has($search)) {
                $results = $cache->get($search);
            } else {
                $query = [
                    'prefix' => $search,
                    'language' => $request->getLocale()
                ];
                $apiResponse = $this->apiProvider->request('GET', '/api/individuals/suggestions', ['query' => $query, 'http_errors' => false]);
                $results = json_decode($apiResponse->getBody(), true);
                $cache->set($search, $results);
            }
            $count = 10;
        } else {
            $page = $request->query->get('page', 1);
            $cache = new FilesystemCache('individual.list.page', 3600);
            if ($cache->has($page) && $cache->has($page . '.count')) {
                $results = $cache->get($page);
                $count = $cache->get($page . '.count');
            } else {
                $query = [
                    'from' => ($page - 1) * 10,
                    'size' => 10,
                    'sort' => 'name:asc',
                    'status' => 'enabled',
                    'language' => $request->getLocale()
                ];
                $apiResponse = $this->apiProvider->request('GET', '/api/individuals', ['query' => $query, 'http_errors' => false]);
                $results = json_decode($apiResponse->getBody(), true);
                $count = (int)$apiResponse->getHeader('X-Total-Count')[0];
                $cache->set($page, $results);
                $cache->set($page . '.count', $count);
            }
        }
        foreach ($results as &$result) {
            $result = [
                'slug' => $result['slug'],
                'name' => 'name'
            ];
        }

        $data = [
            'results' => $results,
            'total_count' => $count
        ];

        $response = new JsonResponse($data);
        $response->setCache([
            'max_age' => 3600,
            's_maxage' => 3600,
            'public' => true
        ]);

        return $response;
    }
}