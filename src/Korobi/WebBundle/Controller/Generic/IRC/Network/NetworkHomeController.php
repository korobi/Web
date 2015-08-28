<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Network;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;

class NetworkHomeController extends BaseController {

    /**
     * @param $network
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function networkAction($network) {
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

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        // fetch all channels
        $dbChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findAllByNetwork($network)
            ->toArray();

        $channels = [];

        // create an entry for each channel
        foreach($dbChannels as $dbChannel) {
            /** @var Channel $dbChannel */

            $channel = $dbChannel->getChannel();
            if($channel == null || empty($channel)) {
                continue;
            }

            // only add channels with keys if we're an admin
            if($dbChannel->getKey() !== null && !$this->authChecker->isGranted('ROLE_PRIVATE_ACCESS')) {
                continue;
            }

            $key = null;
            if ($dbChannel->getKey() !== null && $this->authChecker->isGranted('ROLE_PRIVATE_ACCESS')) {
                $key = $dbChannel->getKey();
            }

            $channels[$channel] = ["url" => $this->generateUrl('channel', [
                'network' => $network,
                'channel' => self::transformChannelName($channel),
            ]), "db" => $dbChannel, 'key' => $key];
        }

        ksort($channels, SORT_NATURAL | SORT_FLAG_CASE);

        return $this->render('KorobiWebBundle:controller/generic/irc/network:network.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'all_channels_private' => empty($channels),
            'channels' => $channels,
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function networksAction() {
        /** @var Network $dbNetwork */
        $dbNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray(false);

        $networks = [];

        $dbChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->countChannelsByNetwork($this->authChecker->isGranted('ROLE_ADMIN'));

        $validNetworks = array_column($dbChannels, 'network');

        // create an entry for each channel
        foreach($dbNetworks as $network) {
            /** @var Network $network */
            if(in_array($network->getSlug(), $validNetworks)) {
                $networks[$network->getName()] = $this->generateUrl('network', [
                    'network' => $network->getSlug(),
                ]);
            }
        }

        ksort($networks, SORT_NATURAL | SORT_FLAG_CASE);

        return $this->render('KorobiWebBundle:controller/generic/irc/network:networks.html.twig', [
            'all_networks_private' => empty($networks),
            'networks' => $networks,
        ]);
    }
}
