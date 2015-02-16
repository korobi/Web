<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelCommand;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class CommandController extends BaseController {

    public function homeAction() {

        $rawNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray();

        $networks = [];
        foreach ($rawNetworks as $network) {
            /** @var $network Network  */
            $name = $network->getSlug();
            if (!in_array($name, $networks)) {
                $networks[$name] = [
                    'name' => $network->getName(),
                    'href' => $this->generateUrl('commands_network', [
                        'network' => $name
                    ])
                ];
            }
        }

        return $this->render('KorobiWebBundle:controller/command:home.html.twig', [
            'networks' => $networks
        ]);
    }

    public function networkAction($network) {
        $isAdmin = $this->authChecker->isGranted('ROLE_ADMIN');

        $rawChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findAllByNetwork($network)
            ->toArray();
        $channels = [];

        foreach ($rawChannels as $channel) {
            /** @var $channel Channel  */

            // only add channels with keys if we're an admin
            if ($channel->getKey() !== null && !$isAdmin) {
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
            'channels' => $channels
        ]);
    }

    public function channelAction(Request $request, $network, $channel) {
        $commands = [];

        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')->getManager()->getRepository('KorobiWebBundle:Channel')->findByChannel($network, '#' . $channel)->toArray(false);
        if (empty($dbChannel)) {
            throw new Exception('Could not find channel');
        }

        // we exist, trim to first entry
        $dbChannel = $dbChannel[0];
        if ($dbChannel->getKey() !== null) {
            if ($request->query->get('key') !== $dbChannel->getKey()) {
                throw new Exception('Unauthorized');
            }
        }

        $rawCommands = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:ChannelCommand')
            ->findAllByChannel(
                $network,
                '#' . $channel // TODO
            )
            ->toArray();


        foreach ($rawCommands as $dbCommand) {
            /** @var $dbCommand ChannelCommand  */
            if ($dbCommand->getIsAlias()) {
                continue;
            }

            $rawAliases = $this->get('doctrine_mongodb')->getManager()
                ->getRepository('KorobiWebBundle:ChannelCommand')
                ->findAliasesFor($network, '#' . $channel, $dbCommand->getName())
            ->toArray();
            $aliases = [];
            foreach ($rawAliases as $alias) {
                /** @var $alias ChannelCommand  */
                $aliases[] = $alias->getName();
            }

            $command = [
                'name' => $dbCommand->getName(),
                'value' => $dbCommand->getValue(),
                'aliases' => implode(', ', $aliases),
                'is_action' => $dbCommand->getIsAction()
            ];
            $commands[] = $command;
        }

        return $this->render('KorobiWebBundle:controller/command:channel.html.twig', [
            'commands' => $commands
        ]);
    }
}
