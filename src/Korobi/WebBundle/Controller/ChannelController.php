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
            $links[] = $this->createLink($dbChannel, $linkBase, 'Logs', $this->generateUrl('channel_logs', $linkBase));
        }

        if ($dbChannel->getCommandsEnabled()) {
            $links[] = $this->createLink($dbChannel, $linkBase, 'Commands', $this->generateUrl('channel_commands', $linkBase));
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

    private function createLink($dbChannel, $linkBase, $name, $href) {
        /** @var Channel $dbChannel */
        $result = [
            'name' => $name,
            'href' => $href
        ];
        if($dbChannel->getKey() !== null && $this->authChecker->isGranted('ROLE_ADMIN')) {
            $result['href'] .= '?key=' . $dbChannel->getKey();
        }

        return $result;
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
        $provided = false;
        list($year, $month, $day, $tail, $provided) = self::populateRequest($year, $month, $day, $tail);

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
            if ($chat->getNotice() && $chat->getNoticeTarget() !== 'NORMAL') {
                continue;
            }

            $chats[] = $this->transformToChatMessage($chat);
        }

        if (in_array("application/json", $request->getAcceptableContentTypes())) {
            return new JsonResponse($chats);
        }

        if (!$provided) {
            $request->getSession()->getFlashBag()->add('notice', self::createLogNotice($dbNetwork->getSlug(), $channel));
        }


        // time to render!
        return $this->render('KorobiWebBundle:controller/channel:logs.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'logs' => $chats,
            'log_date_formatted' => date('F j, Y', mktime(0, 0, 0, $month, $day, $year)),
            'log_date' => date('Y/m/d', mktime(0, 0, 0, $month, $day, $year)),
            'last_id' => empty($chats) ? '' : end($chats)['id'],
            'is_tail' => $tail !== false,
            'available_log_days' => $this->grabAvailableLogDays($dbNetwork->getSlug(), $dbChannel->getChannel())
        ]);
    }

    private static function createLogNotice($slug, $channel) {
        $result = "We're still working on finishing the website - in the meantime, if you're looking for older logs, simply append the date (/yyyy/mm/dd) to the URL. Example: https://korobi.io/";
        $result .= $slug . '/' . $channel . '/logs/' . date('Y/m/d', strtotime('Yesterday'));
        return $result;
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $tail
     * @return array
     */
    private static function populateRequest($year, $month, $day, $tail) {
        $provided = false;
        if (!$year) {
            $year = date('Y');
        } else {
            $provided = true;
        }

        if (!$month) {
            $month = date('m');
        } else {
            $provided = true;
        }

        if (!$day) {
            $day = date('d');
        } else {
            $provided = true;
        }

        if ($tail !== false) {
            // maximum: 90  |  minimum: 5
            if ($tail > 90 || $tail < 5) {
                // fallback to 30
                $tail = 30;
            }
        }

        return [$year, $month, $day, $tail, $provided];
    }

    private function transformToChatMessage(Chat $chat) {
        return [
            'id'         => $chat->getId(),
            'timestamp'  => $chat->getDate()->getTimestamp(),
            'type'       => strtolower($chat->getType()),
            'role'       => $chat->getType() == 'MESSAGE' ? strtolower($chat->getActorPrefix()) : '',
            'nickColour' => LogParser::getColourForActor($chat),
            'displayNick'=> LogParser::getDisplayName($chat),
            'nick'       => LogParser::getActorName($chat),
            'message'    => $this->parseChatMessage($chat)
        ];
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

    /**
     * @param string $network The slug of the network
     * @param string $channel The channel name (Including suitable prefix - e.g. #)
     * @return array An array of available unique days. Think [["day": 1, "month": 1, "year": 2015],["day": 2, "month": 1, "year": 2015]]
     */
    private function grabAvailableLogDays($network, $channel) {
        // We're working from this:
        /*
           [{
                $match: {
                    channel: "#korobi",
                    network: "esper"
                }
            }, {
                $group: {
                    "_id": {
                        "day": {
                            $dayOfMonth: "$date"
                        },
                        month: {
                            $month: "$date"
                        },
                        year: {
                            $year: "$date"
                        }
                    }
                }
            }]
         */

        /** @var \MongoCollection $collection */
        $collection = $this->get('doctrine_mongodb')->getManager()->getDocumentCollection('KorobiWebBundle:Chat')->getMongoCollection();

        // PHP representation of the above
        $pipeline = [
            [
                '$match' => [
                    "channel" => $channel,
                    "network" => $network
                ]
            ],
            [
                '$group' => [
                    "_id" => [
                        "day" => ['$dayOfMonth' => '$date'],
                        "month" => ['$month' => '$date'],
                        "year" => ['$year' => '$date']
                    ]
                ]
            ]
        ];

        // Remove the crap that gets returned
        return array_map(function($item) {
            return $item['_id'];
        }, $collection->aggregate($pipeline)['result']);
    }
}
