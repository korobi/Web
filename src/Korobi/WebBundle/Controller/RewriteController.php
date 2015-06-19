<?php

namespace Korobi\WebBundle\Controller;

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
    public function rewriteAction($network, $channel, $component = 'home', $year = false, $month = false, $day = false, $tail = false, $gameId = false) {
        if ($tail) {
            return $this->redirectToRoute('channel_logs_tail', [
                'network' => $network,
                'channel' => $channel,
                'tail' => $tail,
            ]);
        } else if ($year && $month && $day) {
            return $this->redirectToRoute('channel_logs_date', [
                'network' => $network,
                'channel' => $channel,
                'year' => $year,
                'month' => $month,
                'day' => $day,
            ]);
        } else {
            switch($component) {
                case 'logs':
                    return $this->redirectToRoute('channel_logs', [
                        'network' => $network,
                        'channel' => $channel
                    ]);
                case 'commands':
                    return $this->redirectToRoute('channel_commands', [
                        'network' => $network,
                        'channel' => $channel
                    ]);
                case 'ai':
                    return $this->redirectToRoute('channel_ai', [
                        'network' => $network,
                        'channel' => $channel
                    ]);
                case 'games':
                    return $this->redirectToRoute('channel_games', [
                        'network' => $network,
                        'channel' => $channel,
                        'gameId' => $gameId
                    ]);
            }
        }
    }
}
