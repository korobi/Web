<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;
use Korobi\WebBundle\Parser\NickColours;
use Symfony\Component\Config\Definition\Exception\Exception;
use Symfony\Component\HttpFoundation\Request;

class LogController extends BaseController {

    const ACTION_USER_PREFIX = '*';
    const ACTION_SERVER_PREFIX = '**';
    const ACTION_SERVER_CLASS = 'irc--14-99';

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
            /** @var $network Network  */

            $networks[] = [
                'name' => $network->getName(),
                'href' => $this->generateUrl('logs_network', [
                    'network' => $network->getSlug()
                ])
            ];
        }

        return $this->render('KorobiWebBundle:controller/log:home.html.twig', [
            'networks' => $networks
        ]);
    }

    // TODO:
    // - invalid network name
    // - empty network
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
                'href' => $this->generateUrl('logs_channel', [
                    'network' => $network,
                    'channel' => self::transformChannelName($channel->getChannel())
                ])
            ];
        }

        return $this->render('KorobiWebBundle:controller/log:network.html.twig', [
            'channels' => $channels,
            'network' => $dbNetwork->getName()
        ]);
    }

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @param bool $year
     * @param bool $month
     * @param bool $day
     * @param bool $tail
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function channelAction(Request $request, $network, $channel, $year = false, $month = false, $day = false, $tail = false) {
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

        // validate channel
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, '#' . $channel) // TODO
            ->toArray(false);
        if (empty($dbChannel)) {
            throw new Exception('Could not find channel');
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

        // check if this channel requires a key
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if ($key === null || $key !== $dbChannel->getKey()) {
                throw new Exception('Unauthorized'); // TODO
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

        return $this->render('KorobiWebBundle:controller/log:channel.html.twig', [
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
                $result .= self::transformModeToLetter($chat->getRecipientPrefix());
                $result .= ' ';
                $result .= self::transformActor($chat->getRecipientName());
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
    private static function transformModeToLetter($mode) {
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
