<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelCommand;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Exception\UnsupportedOperationException;
use Korobi\WebBundle\Parser\ChatMessage;
use Korobi\WebBundle\Parser\LogParser;
use Korobi\WebBundle\Repository\ChatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChannelController extends BaseController {

    /**
     * @var \ReflectionClass The log parser reflection class.
     */
    private $logParser;

    // --------------
    // ---- Home ----
    // --------------

    /**
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function homeAction($network, $channel) {
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        // create appropriate links
        $links = [];
        $linkBase = ['network' => $network, 'channel' => $channel];

        if ($dbChannel->getLogsEnabled()) {
            $links[] = [
                'name' => 'Logs',
                'href' => $this->generateUrl('channel_logs', $linkBase)
            ];
        }

        if ($dbChannel->getCommandsEnabled()) {
            $links[] = [
                'name' => 'Commands',
                'href' => $this->generateUrl('channel_commands', $linkBase)
            ];
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

    // ------------------
    // ---- Commands ----
    // ------------------

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
                ->findAliasesFor($network, self::transformChannelName($channel, true), $dbCommand->getName()) // TODO
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
                'is_action' => $dbCommand->getIsAction()
            ];
        }

        // time to render!
        return $this->render('KorobiWebBundle:controller/channel:commands.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'commands' => $commands
        ]);
    }

    // --------------
    // ---- Logs ----
    // --------------

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @param bool $year
     * @param bool $month
     * @param bool $day
     * @param bool $tail
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function logsAction(Request $request, $network, $channel, $year = false, $month = false, $day = false, $tail = false) {
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

        // populate variables with request information if available, or defaults
        // note: validation is done here
        list($year, $month, $day, $tail) = self::populateRequest($year, $month, $day, $tail);

        // fetch all chats
        /** @var ChatRepository $repo */
        $repo = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat');
        $last_id = $request->query->get('last_id', false);
        if($last_id !== false && \MongoId::isValid($last_id)) {
            $dbChats = $repo->findAllByChannelAndId(
                    $network,
                    $dbChannel->getChannel(),
                    new \MongoId($last_id),
                    new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day + 1, $year))))
                )
                ->toArray();
        } else {
            $dbChats = $repo->findAllByChannelAndDate(
                    $network,
                    $dbChannel->getChannel(),
                    new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day, $year)))),
                    new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day + 1, $year))))
                )
                ->toArray();
        }

        // if a tail is requested and no last id was provided...
        if ($tail !== false && $last_id === false) {
            // ... grab the last X chats
            $dbChats = array_slice($dbChats, -$tail);
        }

        // grab reflection class for log parser
        $this->logParser = new \ReflectionClass("Korobi\\WebBundle\\Parser\\LogParser");

        $chats = [];

        // process all found chat entries
        foreach ($dbChats as $chat) {
            /** @var Chat $chat */
            $chats[] = $this->transformToChatMessage($chat);
        }

        if ($request->isXmlHttpRequest()) {
            return new JsonResponse(json_encode($chats));
        }

        // time to render!
        return $this->render('KorobiWebBundle:controller/channel:logs.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'logs' => $chats,
            'log_date' => date('F j, Y', mktime(0, 0, 0, $month, $day, $year)),
            'last_id' => empty($chats) ? '' : end($chats)->getId(),
            'is_tail' => $tail !== false
        ]);
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $tail
     * @return array
     */
    private static function populateRequest($year, $month, $day, $tail) {
        if (!$year) {
            $year = date('Y');
        }

        if (!$month) {
            $month = date('n');
        }

        if (!$day) {
            $day = date('d');
        }

        if ($tail !== false) {
            // maximum: 90  |  minimum: 5
            if ($tail > 90 || $tail < 5) {
                // fallback to 30
                $tail = 30;
            }
        }

        return [$year, $month, $day, $tail];
    }

    private function transformToChatMessage(Chat $chat) {
        return new ChatMessage(
            $chat->getId(),
            $chat->getDate(),
            $chat->getType() == 'MESSAGE' ? $chat->getActorPrefix() : '',
            LogParser::getColourForActor($chat),
            LogParser::getActorName($chat),
            $this->parseChatMessage($chat)
        );
    }

    /**
     * @param Chat $chat The chat entry to pass off to the parser.
     * @return string
     * @throws UnsupportedOperationException If you try and parse an unsupported message type.
     */
    private function parseChatMessage(Chat $chat) {
        $method = 'parse' . ucfirst(strtolower($chat->getType()));
        try {
            $method = $this->logParser->getMethod($method);
            return $method->invokeArgs(null, [$chat]);
        } catch (\ReflectionException $ex) {
            throw new UnsupportedOperationException("The method $method caused a reflection exception: " . $ex->getMessage());
        }
    }
}
