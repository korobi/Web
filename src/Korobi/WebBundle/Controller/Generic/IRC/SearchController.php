<?php

namespace Korobi\WebBundle\Controller\Generic\IRC;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Repository\ChannelRepository;
use Korobi\WebBundle\Repository\ChatRepository;
use Korobi\WebBundle\Repository\NetworkRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseController {

    const RESULT_PER_PAGE = 20;

    public function searchAction(Request $request) {
        $term = $request->get("term");
        $page = (int) $request->get("page", 0) - 1;
        if ($page < 0) $page = 0;

        /** @var ChannelRepository $channelRepo */
        $channelRepo = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel');

        $dbChannels = $channelRepo->findPublicChannels()->toArray(false);
        $channels = array_map(function(Channel $channel) {
            return $channel->getNetwork() . $channel->getChannel();
        }, $dbChannels);

        /** @var \MongoCollection $collection */
        $collection = $this
            ->get('doctrine_mongodb')
            ->getManager()
            ->getDocumentCollection('KorobiWebBundle:Chat')
            ->getMongoCollection();

        $pipeline = [
            [
                '$match' => [
                    'type' => ['$in' => ['MESSAGE', 'ACTION']],
                    '$text' => ['$search' => $term]
                ]
            ], [
                '$project' => [
                    'network_channel' => ['$concat' => ['$network', '$channel']],
                    'date'=> 1,
                    'actor_hostname'=> 1,
                    'actor_name'=> 1,
                    'message'=> 1,
                    'type'=> 1,
                ]
            ], [
                '$match' => ['network_channel' => ['$nin' => $channels]]
            ], [
                '$sort' => ['date' => -1]
            ], [
                '$skip' => self::RESULT_PER_PAGE * $page
            ], [
                '$limit' => self::RESULT_PER_PAGE
            ]
        ];


        var_dump($collection->aggregate($pipeline)['result']);
        die();
    }

    /**
     * @return ChatRepository
     */
    private function getChatRepository() {
        return $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat');
    }
}
