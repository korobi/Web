<?php

namespace Korobi\WebBundle\Controller\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class ChannelHomeController extends BaseController {

    /**
     * @Route("/channel/{network}/{channel}/", name = "channel")
     *
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction($network, $channel) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        // create appropriate links
        $links = [];
        $linkBase = ['network' => $network, 'channel' => $channel];

        if ($dbChannel->getLogsEnabled()) {
            $links[] = $this->createLink($dbChannel, 'Logs', $this->generateUrl('channel_logs', $linkBase));
        }

        if ($dbChannel->getCommandsEnabled()) {
            $links[] = $this->createLink($dbChannel, 'Commands', $this->generateUrl('channel_commands', $linkBase));
        }

        // time to render!
        return $this->render('KorobiWebBundle:controller/channel:home.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'slug' => self::transformChannelName($dbChannel->getChannel()),
            'command_prefix' => $dbChannel->getCommandPrefix(),
            'links' => $links
        ]);
    }

    private function createLink($dbChannel, $name, $href) {
        /** @var Channel $dbChannel */
        $result = [
            'name' => $name,
            'href' => $href
        ];
        if($dbChannel->getKey() !== null && $this->getAuthChecker()->isGranted('ROLE_ADMIN')) {
            $result['href'] .= '?key=' . $dbChannel->getKey();
        }

        return $result;
    }
}
