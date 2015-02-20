<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelCommand;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Exception\UnsupportedOperationException;
use Korobi\WebBundle\Parser\LogParser;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Intl\Exception\NotImplementedException;

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
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);

        // make sure we actually have a network
        if (empty($dbNetwork)) {
            throw $this->createNotFoundException('Could not find network');
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        // fetch channel
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, self::transformChannelName($channel, true))
            ->toArray(false);

        // make sure we actually have a channel
        if (empty($dbChannel)) {
            throw $this->createNotFoundException('Could not find channel');
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

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
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);

        // make sure we actually have a network
        if (empty($dbNetwork)) {
            throw $this->createNotFoundException('Could not find network');
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        // fetch channel
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, self::transformChannelName($channel, true))
            ->toArray(false);

        // make sure we actually have a channel
        if (empty($dbChannel)) {
            throw $this->createNotFoundException('Could not find channel');
        }

        // we exist, trim to first entry
        $dbChannel = $dbChannel[0];

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
            ->findAllByChannel($network, self::transformChannelName($channel, true))
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
                ->findAliasesFor($network, self::transformChannelName($channel, true), $dbCommand->getName()) // TODO
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
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);

        // make sure we actually have a network
        if (empty($dbNetwork)) {
            throw $this->createNotFoundException('Could not find network');
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        // fetch channel
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, self::transformChannelName($channel, true)) // TODO
            ->toArray(false);

        // make sure we actually have a channel
        if (empty($dbChannel)) {
            throw $this->createNotFoundException('Could not find channel');
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

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

        // fetch all commands
        $dbChats = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat')
            ->findAllByChannelAndDate(
                $network,
                self::transformChannelName($channel, true),
                new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day, $year)))),
                new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day + 1, $year))))
            )
            ->toArray();

        // if a tail is requested...
        if ($tail !== false) {
            // ... grab the last X chats
            $dbChats = array_slice($dbChats, -$tail);
        }

        // grab reflection class for log parser
        $this->logParser = new \ReflectionClass("Korobi\\WebBundle\\Parser\\LogParser");

        $chats = [];

        // process all found chat entries
        $index = 1;
        foreach ($dbChats as $chat) {
            /** @var $chat Chat  */

            $result = '<span class="logs--line js-hl" data-line-num="' . $index++ . '"><i class="fa fa-paint-brush"></i> ';

            $result .= $this->parseChatEntry($chat);

            $result .= '</span>';

            $chats[] = $result;
        }

        if ($request->isXmlHttpRequest()) {
            // <span class="logs--line js-hl" data-line-num="{{ loop.index }}"><i class="fa fa-paint-brush"></i> {{ message|raw }}</span>
            return new JsonResponse(json_encode($chats));
        }

        // time to render!
        return $this->render('KorobiWebBundle:controller/channel:logs.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'logs' => $chats,
            'log_date' => date('F j, Y', mktime(0, 0, 0, $month, $day, $year)),
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

    /**
     * @param Chat $chat The chat entry to pass off to the parser.
     * @return string
     * @throws UnsupportedOperationException If you try and parse an unsupported message type.
     */
    private function parseChatEntry(Chat $chat) {
        $method = ucfirst(strtolower($chat->getType()));
        try {
            $method = $this->logParser->getMethod($method);
            $method->invokeArgs(null, [$chat]);
        } catch (\ReflectionException $ex) {
            throw new UnsupportedOperationException();
        }


    }
}
