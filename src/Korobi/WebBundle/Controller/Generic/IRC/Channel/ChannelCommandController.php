<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelCommand;
use Korobi\WebBundle\Document\Network;
use Symfony\Component\HttpFoundation\Request;

class ChannelCommandController extends BaseController {

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function commandsAction(Request $request, $network, $channel) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        // check if this channel requires a key
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if ($key === null || $key !== $dbChannel->getKey()) {
                throw new \Exception('Unauthorized'); // TODO
            }
        }

        // fetch all commands
        $dbCommands = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:ChannelCommand')
            ->findAllByChannel($network, $dbChannel->getChannel())
            ->toArray();

        $commands = [];

        // process all found commands
        foreach ($dbCommands as $dbCommand) {
            /** @var ChannelCommand $dbCommand */

            // skip if this command is an alias
            if ($dbCommand->getIsAlias()) {
                continue;
            }

            // fetch aliases for this command
            $rawAliases = $this->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('KorobiWebBundle:ChannelCommand')
                ->findAliasesFor($network, self::transformChannelName($channel, true), $dbCommand->getName())
                ->toArray();

            $aliases = [];
            foreach ($rawAliases as $alias) {
                /** @var ChannelCommand $alias */
                $aliases[] = $alias->getName();
            }

            $commands[] = [
                'name' => $dbCommand->getName(),
                'value' => $dbCommand->getValue(),
                'aliases' => implode(', ', $aliases),
                'is_action' => $dbCommand->getIsAction(),
            ];
        }

        // time to render!
        return $this->render('KorobiWebBundle:controller/generic/irc/channel:commands.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'channel_private' => $dbChannel->isPrivate(),
            'commands' => $commands,
        ]);
    }
}
