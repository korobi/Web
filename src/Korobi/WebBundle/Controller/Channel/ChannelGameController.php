<?php

namespace Korobi\WebBundle\Controller\Channel;

use Korobi\WebBundle\Document\CAHGame;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Repository\CAHGameRepository;
use Symfony\Component\HttpFoundation\Request;

class ChannelGameController extends BaseController {

    const CAH_NETWORK = 'esper';
    const CAH_CHANNEL = '#CardsAgainstHumanity';

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @param $gameId
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function gameAction(Request $request, $network, $channel, $gameId) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        // check if this channel requires a key
        if($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if($key === null || $key !== $dbChannel->getKey()) {
                throw new \Exception('Unauthorized'); // TODO
            }
        }

        if($dbNetwork->getSlug() == self::CAH_NETWORK && $dbChannel->getChannel() == self::CAH_CHANNEL) {
            $request->getSession()->getFlashBag()->add('notice', 'Please note that CAH game statistics are not complete and are subject to changes.');
            return $this->cardsAgainstHumanity($dbNetwork, $dbChannel, $gameId);
        } else {
            throw $this->createNotFoundException('Invalid game channel.');
        }
    }

    // --------------------------------
    // ---- Cards Against Humanity ----
    // --------------------------------
    /**
     * @param $dbNetwork
     * @param $dbChannel
     * @param $gameId
     * @return \Symfony\Component\HttpFoundation\Response
     */
    private function cardsAgainstHumanity($dbNetwork, $dbChannel, $gameId) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */

        /** @var CAHGameRepository $repo */
        $repo = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:CAHGame');
        /** @var CAHGame $game */
        $game = $repo->findById($gameId)->toArray(false);
        if(empty($game)) {
            throw $this->createNotFoundException('Game not found.');
        }

        // grab first slice
        $game = $game[0];
        $game = clone $game;

        $rounds = [];
        foreach($game->getRounds() as $round) {
            if(!array_key_exists('czar', $round)) {
                continue;
            }

            $rounds[] = $round;
        }
        $game->setRounds($rounds);

        // Assign the game state a human-friendly string
        $state = '';
        switch($game->getState()) {
            case 'FINISHED':
                $state = 'Finished';
                break;
            case 'ENDED_PLAYERS_START':
                $state = 'Ended (not enough players to start)';
                break;
            case 'ENDED_PLAYERS_CONTINUE':
                $state = 'Ended (not enough players to continue)';
                break;
            case 'ENDED_WHITE_CARDS':
                $state = 'Ended (not enough white cards to continue)';
                break;
            case 'ENDED_BLACK_CARDS':
                $state = 'Ended (not enough black cards to continue)';
                break;
            case 'ENDED_BOT_LEFT':
                $state = 'Ended (game bot left the room)';
                break;
        }

        // Put card counts into a certain order, and assign a human-friendly string as the key
        $counts = ['Unused Black' => 0, 'Total Black' => 0, 'Unused White' => 0, 'Total White' => 0];
        $percent = [];
        foreach($game->getCardCounts() as $key => $value) {
            if($key == 'black_unused') {
                $counts['Unused Black'] = $value;
            } else if($key == 'black_total') {
                $counts['Total Black'] = $value;
            } else if($key == 'white_unused') {
                $counts['Unused White'] = $value;
            } else if($key == 'white_total') {
                $counts['Total White'] = $value;
            }
        }

        $percent['used_black'] = 100 * (1-($counts['Unused Black'] / $counts['Total Black']));
        $percent['used_white'] = 100 * (1-($counts['Unused White'] / $counts['Total White']));

        // time to render!
        return $this->render('KorobiWebBundle:controller/channel/game:cah.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'game_id' => $gameId,
            'game' => $game,
            'game_state' => $state,
            'card_counts' => $counts,
            'percents' => $percent
        ]);
    }
}
