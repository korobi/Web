<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelCommand;
use Korobi\WebBundle\Document\Network;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class CommandController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        // fetch all networks from the database
        $rawNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray();

        $networks = [];

        // create an entry for each network
        foreach ($rawNetworks as $network) {
            /** @var $network Network */

            $networks[] = [
                'name' => $network->getName(),
                'href' => $this->generateUrl('commands_network', [
                    'network' => $network->getSlug()
                ])
            ];
        }

        return $this->render('KorobiWebBundle:controller/command:home.html.twig', [
            'networks' => $networks
        ]);
    }

    /**
     * @param $network
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function networkAction($network) {
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);
        if (empty($dbNetwork)) {
            throw new Exception('Could not find network'); // TODO
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        $rawChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findAllByNetwork($network)
            ->toArray();

        $channels = [];

        // create an entry for each channel
        foreach ($rawChannels as $channel) {
            /** @var $channel Channel  */

            // only add channels with keys if we're an admin
            if ($channel->getKey() !== null && !$this->authChecker->isGranted('ROLE_ADMIN')) {
                continue;
            }

            $channels[] = [
                'name' => $channel->getChannel(),
                'href' => $this->generateUrl('commands_channel', [
                    'network' => $network,
                    'channel' => self::transformChannelName($channel->getChannel())
                ])
            ];
        }

        return $this->render('KorobiWebBundle:controller/command:network.html.twig', [
            'channels' => $channels,
            'network' => $dbNetwork->getName()
        ]);
    }

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function channelAction(Request $request, $network, $channel) {
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, '#' . $channel)
            ->toArray(false);

        if (empty($dbChannel)) {
            throw new Exception('Could not find channel'); // TODO
        }

        // we exist, trim to first entry
        $dbChannel = $dbChannel[0];

        // check if this channel requires a key
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if ($key === null || $key !== $dbChannel->getKey()) {
                throw new Exception('Unauthorized'); // TODO
            }
        }

        // fetch all commands
        $dbCommands = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:ChannelCommand')
            ->findAllByChannel($network, '#' . $channel) // TODO
            ->toArray();

        $commands = [];

        // process all found commands
        foreach ($dbCommands as $dbCommand) {
            /** @var $dbCommand ChannelCommand  */

            // skip if this command is an alias
            if ($dbCommand->getIsAlias()) {
                continue;
            }

            // fetch aliases for this command
            $rawAliases = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('KorobiWebBundle:ChannelCommand')
                ->findAliasesFor($network, '#' . $channel, $dbCommand->getName()) // TODO
                ->toArray();

            $aliases = [];
            foreach ($rawAliases as $alias) {
                /** @var $alias ChannelCommand  */
                $aliases[] = $alias->getName();
            }

            $commands[] = [
                'name' => $dbCommand->getName(),
                'value' => $dbCommand->getValue(),
                'aliases' => implode(', ', $aliases),
                'is_action' => $dbCommand->getIsAction()
            ];
        }

        return $this->render('KorobiWebBundle:controller/command:channel.html.twig', [
            'commands' => $commands
        ]);
    }
}
