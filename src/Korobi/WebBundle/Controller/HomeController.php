<?php

namespace Korobi\WebBundle\Controller;

class HomeController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        $dbChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->getRecentlyActiveChannels(5)
            ->toArray();

        $channels = [];
        $now = time();
        foreach($dbChannels as $channel) {
            $channels[$channel->getChannel()] = $this->relativeTime($now, $channel->getLastValidContentAt()->getTimestamp());
        }

        return $this->render('KorobiWebBundle::home.html.twig', ["channels" => $channels]);
    }

    private function relativeTime($current, $previous) {
        $msPerMinute = 60;
        $msPerHour = $msPerMinute * 60;
        $msPerDay = $msPerHour * 24;
        $msPerMonth = $msPerDay * 30;
        $msPerYear = $msPerDay * 365;

        $elapsed = $current - $previous;

        if ($elapsed < $msPerMinute) {
            return round($elapsed) . ' seconds ago';
        } else if ($elapsed < $msPerHour) {
            return round($elapsed/$msPerMinute) . ' minutes ago';
        } else if ($elapsed < $msPerDay ) {
            return round($elapsed/$msPerHour ) . ' hours ago';
        } else if ($elapsed < $msPerMonth) {
            return round($elapsed/$msPerDay) . ' days ago';
        } else if ($elapsed < $msPerYear) {
            return round($elapsed/$msPerMonth) . ' months ago';
        } else {
            return round($elapsed/$msPerYear ) . ' years ago';
        }
    }

}
