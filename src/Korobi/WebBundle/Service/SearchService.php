<?php

namespace Korobi\WebBundle\Service;

use Elasticsearch\Client;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Repository\NetworkRepository;
use Korobi\WebBundle\Search\ChannelSuggestion;

class SearchService implements ISearchService {

    /**
     * @var Client
     */
    private $client;
    /**
     * @var NetworkRepository
     */
    private $networkRepository;
    /**
     * @var array slug => friendly name
     */
    private $networkNameCache;

    /**
     * SearchService constructor.
     * @param Client $client
     * @param NetworkRepository $networkRepository
     */
    public function __construct(Client $client, NetworkRepository $networkRepository) {
        $this->client = $client;
        $this->networkRepository = $networkRepository;
        $this->createNetworkNameCache();
    }

    /**
     * Gets autocomplete suggestions for a given fragment of a channel name.
     *
     * @param string $channelName Part of the channel name.
     * @return ChannelSuggestion[]
     */
    public function getSuggestionsForChannelName($channelName) {
        $params = [
            'index' => 'channels',
            'body' => [
                'suggest' => [
                    'text' => $channelName,
                    'completion' => [
                        'field' => '_name_suggest'
                    ],
                ],
            ],
        ];

        $results = $this->client->suggest($params);
        $suggestions = array_map(function($suggestion) {
            $payload = $suggestion['payload'];
            $friendlyNetworkName = $this->networkNameCache[$payload['network']];
            return new ChannelSuggestion(
                $payload['channel'], $payload['network'], $friendlyNetworkName, $payload['mongoId']
            );
        }, $results['suggest'][0]['options']);
        return $suggestions;
    }

    private function createNetworkNameCache() {
        /**
         * @var $networks Network[]
         */
        $networks = $this->networkRepository->findAll();
        array_walk($networks, function($network) {
            $this->networkNameCache[$network->getSlug()] = $network->getName();
        });
    }
}
