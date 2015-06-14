<?php

namespace Korobi\WebBundle\Controller\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Exception\UnsupportedOperationException;
use Korobi\WebBundle\Parser\LogParser;
use Korobi\WebBundle\Repository\ChatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChannelLogController extends BaseController {

    const MAX_NICK_LENGTH = 10;

    /**
     * @var \ReflectionClass The log parser reflection class.
     */
    private $logParser;

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
            'first_for_channel' => $repo->findFirstByChannel($dbNetwork->getSlug(), $dbChannel->getChannel()),
            'tail_url' => $this->generateUrl('channel_logs_tail', ['network' => $network, 'channel' => $channel]),
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
        $nick = LogParser::getDisplayName($chat);
        return [
            'id'         => $chat->getId(),
            'timestamp'  => $chat->getDate()->getTimestamp(),
            'type'       => strtolower($chat->getType()),
            'role'       => $chat->getType() == 'MESSAGE' ? strtolower($chat->getActorPrefix()) : '',
            'nickColour' => LogParser::getColourForActor($chat),
            'displayNick'=> substr($nick, 0, self::MAX_NICK_LENGTH + 1),
            'realNick'   => $nick,
            'nickTooLong'=> strlen($nick) - self::MAX_NICK_LENGTH > 1,
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
            }, {
                $project: {
                    hasAMessage: {
                        $setIsSubset: [["MESSAGE"], "$test"]
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
                    ],
                    "test" => [
                        '$addToSet' => '$type'
                    ]
                ]
            ],
            [
                '$project' => [
                    "hasAMessage" => [
                        '$setIsSubset' => [
                            ["MESSAGE"], '$test'
                        ]
                    ]
                ]
            ]
        ];

        // Remove the crap that gets returned
        return array_map(function($item) {
            return array_merge($item['_id'], ["hasAMessage" => $item['hasAMessage']]);
        }, $collection->aggregate($pipeline)['result']);
    }
}
