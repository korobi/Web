<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;

class NetworkController extends BaseController {

    /**
     * @param $network
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function networkAction($network) {
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);

        // make sure we actually have a network
        if (empty($dbNetwork)) {
            return $this->createNotFoundException();
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
        foreach ($dbChannels as $channel) {
            /** @var $channel Channel  */

            // only add channels with keys if we're an admin
            if ($channel->getKey() !== null && !$this->authChecker->isGranted('ROLE_ADMIN')) {
                continue;
            }

            $channels[] = [
                'name' => $channel->getChannel(),
                'href' => $this->generateUrl('channel', [
                    'network' => $network,
                    'channel' => self::transformChannelName($channel->getChannel())
                ])
            ];
        }

        return $this->render('KorobiWebBundle:controller/network:network.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'all_channels_private' => empty($channels),
            'channels' => $channels
        ]);
    }

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function networksAction() {
        /** @var $dbNetwork Network */
        $dbNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray(false);

        $networks = [];

        // create an entry for each channel
        foreach ($dbNetworks as $network) {
            /** @var $network Network  */

            // fetch all channels
            $dbChannels = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('KorobiWebBundle:Channel')
                ->findAllByNetwork($network->getSlug())
                ->toArray();

            $channels = [];

            // create an entry for each channel
            foreach ($dbChannels as $channel) {
                /** @var $channel Channel  */

                // only add channels with keys if we're an admin
                if ($channel->getKey() !== null && !$this->authChecker->isGranted('ROLE_ADMIN')) {
                    continue;
                }

                $channels[] = $channel;
            }

            if (!empty($channels)) {
                $networks[] = [
                    'name' => $network->getName(),
                    'href' => $this->generateUrl('network', [
                        'network' => $network->getSlug()
                    ])
                ];
            }
        }

        return $this->render('KorobiWebBundle:controller/network:networks.html.twig', [
            'all_networks_private' => empty($networks),
            'networks' => $networks
        ]);
    }
}
