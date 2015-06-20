<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Network;

/**
 * This controller is to rewrite legacy routes to new routes.
 */
class RewriteController extends BaseController {

    /**
     * @param $network
     * @param $channel
     * @param string $component
     * @param bool|int $year
     * @param bool|int $month
     * @param bool|int $day
     * @param bool $tail
     * @param bool $gameId
     * @return \Symfony\Component\HttpFoundation\Response
     * @internal param Request $request
     */
    public function rewriteAction($network, $channel = null, $component = 'home', $year = false, $month = false, $day = false, $tail = false, $gameId = false) {
        // If no channel is provided we assume we're looking for a network.
        if ($channel === null) {
            // Instead of blindly redirecting to the network route, first make sure the network slug is valid. If the
            // slug is not a valid network slug, create a FOUR OH FOUR; otherwise redirect.
            /** @var Network $dbNetwork */
            $dbNetwork = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('KorobiWebBundle:Network')
                ->findNetwork($network)
                ->toArray(false);

            // make sure we actually have a network
            if(empty($dbNetwork)) {
                throw $this->createNotFoundException();
            }

            return $this->redirectToRoute('network', [
                'network' => $network
            ], 301);
        }

        if ($tail) {
            return $this->redirectToRoute('channel_log_tail', [
                'network' => $network,
                'channel' => $channel,
                'tail' => $tail,
            ], 301);
        } else if ($year && $month && $day) {
            return $this->redirectToRoute('channel_log_date', [
                'network' => $network,
                'channel' => $channel,
                'year' => $year,
                'month' => $month,
                'day' => $day,
            ], 301);
        } else {
            switch($component) {
                case 'logs':
                    return $this->redirectToRoute('channel_log', [
                        'network' => $network,
                        'channel' => $channel
                    ], 301);
                case 'commands':
                    return $this->redirectToRoute('channel_command', [
                        'network' => $network,
                        'channel' => $channel
                    ], 301);
                case 'ai':
                    return $this->redirectToRoute('channel_ai', [
                        'network' => $network,
                        'channel' => $channel
                    ], 301);
                case 'games':
                    return $this->redirectToRoute('channel_game', [
                        'network' => $network,
                        'channel' => $channel,
                        'gameId' => $gameId
                    ], 301);
                default:
                    return $this->redirectToRoute('channel', [
                        'network' => $network,
                        'channel' => $channel
                    ], 301);
            }
        }
    }
}
