<?php

namespace Korobi\WebBundle\Controller\Generic\IRC;

use Elasticsearch\Client;
use Korobi\WebBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseController {

    const RESULT_PER_PAGE = 20;

    private $client;

    public function __construct() {
        $this->client = new Client();
    }

    public function searchAction(Request $request) {
        $term = $request->get("term");
        $page = (int) $request->get("page", 1) - 1;
        if ($page < 0) $page = 0;

        ini_set('xdebug.var_display_max_depth', 10);

        $plz = [];

        $plz['count'] = $this->client->count([
            'index' => 'chats',
            'type' => 'chat',
        ])['count'];

        try {
            $plz['suggestChannel'] = $this->suggestChannel($term)['suggest'];
            $plz['searchChat'] = $this->searchChat($term)['hits']['hits'];
        } catch(\Exception $e) {
            print_r(json_decode($e->getMessage()));
        }

        return $this->render('KorobiWebBundle:controller/generic:search.html.twig', [
            'plz' => $plz,
        ]);
    }

    private function suggestChannel($term) {
        $params = [
            'index' => 'channels',
            'body' => [
                'suggest' => [
                    'text' => $term,
                    'completion' => [
                        'field' => '_name_suggest'
                    ],
                ],
            ],
        ];

        return $this->client->suggest($params);
    }

    private function searchChat($term) {
        $params = [
            'index' => 'chats',
            'body' => [
                'query' => [
                    'filtered' => [
                        'query' => [
                            'match_all' => [],
                        ],
                        'filter' => [
                            'term' => [
                                'message' => $term,
                            ],
                        ],
                    ],
                ],
            ],
            'size' => 3,
        ];
        return $this->client->search($params);
    }

}
