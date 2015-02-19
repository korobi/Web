<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelCommand;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;
use Korobi\WebBundle\Parser\NickColours;
use Symfony\Component\HttpFoundation\Request;

class ChannelController extends BaseController {

    const ACTION_USER_PREFIX = '*';
    const ACTION_SERVER_PREFIX = '**';
    const ACTION_SERVER_CLASS = 'irc--14-99';

    public function homeAction($network, $channel) {
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);
        if (empty($dbNetwork)) {
            throw new \Exception('Could not find network'); // TODO
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, '#' . $channel)
            ->toArray(false);

        if (empty($dbChannel)) {
            throw new \Exception('Could not find channel'); // TODO
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

        $slug = $dbChannel->getChannel();

        $links = [];
        $linkBase = [
            'network' => $network,
            'channel' => $channel
        ];

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


        return $this->render('KorobiWebBundle:controller/channel:home.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'slug' => $slug,
            'command_prefix' => $dbChannel->getCommandPrefix(),
            'links' => $links
        ]);
    }

    public function commandsAction(Request $request, $network, $channel) {
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);
        if (empty($dbNetwork)) {
            throw new \Exception('Could not find network'); // TODO
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, '#' . $channel)
            ->toArray(false);

        if (empty($dbChannel)) {
            throw new \Exception('Could not find channel'); // TODO
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

        return $this->render('KorobiWebBundle:controller/channel:commands.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'commands' => $commands
        ]);
    }

    public function logsAction(Request $request, $network, $channel, $year = false, $month = false, $day = false, $tail = false) {
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);
        if (empty($dbNetwork)) {
            throw new \Exception('Could not find network'); // TODO
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        // validate channel
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, '#' . $channel) // TODO
            ->toArray(false);
        if (empty($dbChannel)) {
            throw new \Exception('Could not find channel');
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
                '#' . $channel, // TODO
                new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day, $year)))),
                new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, $month, $day + 1, $year))))
            )
            ->toArray();

        // if a tail is requested...
        if ($tail !== false) {
            // ... grab the last X chats
            $dbChats = array_slice($dbChats, -$tail);
        }

        $chats = [];

        // process all found chat entries
        foreach ($dbChats as $chat) {
            /** @var $chat Chat  */

            switch ($chat->getType()) {
                case 'ACTION':
                    $chats[] = $this->parseAction($chat);
                    break;
                case 'JOIN':
                    $chats[] = $this->parseJoin($chat);
                    break;
                case 'KICK':
                    $chats[] = $this->parseKick($chat);
                    break;
                case 'MESSAGE':
                    $chats[] = $this->parseMessage($chat);
                    break;
                case 'MODE':
                    $chats[] = $this->parseMode($chat);
                    break;
                case 'NICK':
                    $chats[] = $this->parseNick($chat);
                    break;
                case 'PART':
                    $chats[] = $this->parsePart($chat);
                    break;
                case 'QUIT':
                    $chats[] = $this->parseQuit($chat);
                    break;
                case 'TOPIC':
                    $chats[] = $this->parseTopic($chat);
                    break;
            }

        }

        return $this->render('KorobiWebBundle:controller/channel:logs.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'logs' => $chats
        ]);
    }

    // -----------------
    // ---- Parsing ----
    // -----------------

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseAction(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= self::ACTION_USER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' ';

        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseJoin(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' joined the channel';
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseKick(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' was kicked by ';
        $result .= $chat->getActorName();

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseMessage(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '&lt;';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::getSpanForColour(NickColours::getColourForNick(self::transformActor($chat->getActorName())), self::transformActor($chat->getActorName()));
        $result .= '&gt; ';

        // message
        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseMode(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' sets mode ' . $chat->getMessage();
        } else {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' sets mode ' . $chat->getMessage();

            if ($chat->getRecipientPrefix() !== null) {
                $result .= self::transformUserModeToLetter($chat->getRecipientPrefix());
                $result .= ' ';
                $result .= self::transformActor($chat->getRecipientName());
            } else if ($chat->getChannelMode() !== null) {
                $result .= self::transformChannelModeToLetter($chat->getChannelMode());
                $result .= ' ';
                $result .= self::transformActor($chat->getRecipientHostname());
            }
        }
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseNick(Chat $chat) {
        $result = '';
        $prefix = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= $prefix;
        $result .= self::transformActor($chat->getActorName());
        $result .= ' is now known as ';
        $result .= $prefix;
        $result .= $chat->getRecipientName();

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parsePart(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' left the channel';
        $result .= '</span>';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseQuit(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';
        $result .= self::createUserMode($chat->getActorPrefix());
        $result .= self::transformActor($chat->getActorName());
        $result .= ' ';
        $result .= '</span>';

        $result .= 'has quit (' . $chat->getMessage() . ')';

        return $result;
    }

    /**
     * @param Chat $chat
     * @return string
     */
    private function parseTopic(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '<span class="' . self::ACTION_SERVER_CLASS . '">';
        $result .= self::ACTION_SERVER_PREFIX;
        $result .= ' ';

        if ($chat->getActorName() === Chat::ACTOR_INTERNAL) {
            $result .= 'Topic is: ' . $chat->getMessage();
        } else {
            $result .= self::createUserMode($chat->getActorPrefix());
            $result .= self::transformActor($chat->getActorName());
            $result .= ' has changed the topic to: ' . $chat->getMessage();
        }

        $result .= '</span>';

        return $result;
    }

    // -----------------
    // ---- Helpers ----
    // -----------------

    /**
     * @param $colour
     * @param $text
     * @return string
     */
    private static function getSpanForColour($colour, $text) {
        return '<span class="irc--' . $colour . '-99">' . $text . '</span>';
    }

    /**
     * @param $prefix
     * @return string
     */
    private static function createUserMode($prefix) {
        switch ($prefix) {
            case 'OWNER':
                return '<span class="irc--04-99">~</span>';
            case 'ADMIN':
                return '<span class="irc--11-99">&</span>';
            case 'OPERATOR':
                return '<span class="irc--09-99">@</span>';
            case 'HALF_OP':
                return '<span class="irc--13-99">%</span>';
            case 'VOICE':
                return '<span class="irc--08-99">+</span>';
            case 'NORMAL':
            default:
                return '';
        }
    }

    /**
     * @param $mode
     * @return string
     */
    private static function transformChannelModeToLetter($mode) {
        switch ($mode) {
            case 'BAN':
                return 'b';
            case 'QUIET':
                return 'q';
            case 'NORMAL':
            default:
                return '';
        }
    }

    /**
     * @param $mode
     * @return string
     */
    private static function transformUserModeToLetter($mode) {
        switch ($mode) {
            case 'OWNER':
                return 'q';
            case 'ADMIN':
                return 'a';
            case 'OPERATOR':
                return 'o';
            case 'HALF_OP':
                return 'h';
            case 'VOICE':
                return 'v';
            case 'NORMAL':
            default:
                return '';
        }
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
            // minimum: 5
            // maximum: 90
            if ($tail > 90 || $tail < 5) {
                // fallback to 30
                $tail = 30;
            }
        }

        return [$year, $month, $day, $tail];
    }
}
