<?php

namespace Korobi\WebBundle\Controller\Generic\IRC;

use Elasticsearch\Client;
use Korobi\WebBundle\Controller\BaseController;
use Symfony\Component\DependencyInjection\ParameterBag\ParameterBag;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseController {

    const RESULT_PER_PAGE = 20;

    private $client;

    public function __construct() {
        $this->client = new Client(); // FIXME: this shouldn't be instantiated here!
    }

    public function autocompleteAction(Request $request) {
        $suggestions = $this->suggestChannel($request->get("q"));

        $suggestions = array_map(function($suggestion) {
            return $suggestion['payload'];
        }, $suggestions['suggest'][0]['options']);
        return new JsonResponse($suggestions);
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
            //$plz['suggestChannel'] = $this->suggestChannel($term)['suggest'];
            $plz['search'] = $this->search($request)['hits']['hits'];
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

    private function search(Request $request) {
        $query = [];

        // TODO there can be multiple types!
        foreach(['message', 'channel', 'type', 'actor_name', 'start', 'end'] as $name) {
            if($tmp = $request->get($name)) {
                switch($name) {
                    // TODO Timezone stuff
                    // TODO That doesn't work at all
                    case 'start':
                        $query[] = ['range' => ['date' => [
                            'gt' => (new \DateTime($tmp ?: '@0'))->getTimestamp() * 1000
                        ]]];
                        break;
                    case 'end':
                        $query[] = ['range' => ['date' => [
                            'lt' => (new \DateTime($tmp ?: 'now'))->getTimestamp() * 1000
                        ]]];
                        break;
                    case 'type':
                        $query[] = ['term' => [$name => $tmp]];
                        break;
                    default:
                        $query[] = ['match' => [$name => $tmp]];
                }
            }
        }

        if(empty($query)) {
            return ['hits' => ['hits' => []]]; // kek
        }

        $params = [
            'index' => 'chats',
            'body' => [
                'query' => [
                    'filtered' => [
                        'query' => [
                            'bool' => [
                                'must' => $query,
                            ]
                        ],
                        'filter' => [
                            // TODO: filters after query
                        ]
                    ],
                ],
                // TODO That doesn't work yet
                'sort' => [
                    'date' => 'desc'
                ]
            ],
            'size' => 20,
        ];
        var_dump($params);
        return $this->client->search($params);
    }

}
