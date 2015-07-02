<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\ChatIndex;
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
        $showingCurrent = !$year;
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
        $this->logParser = new \ReflectionClass('Korobi\\WebBundle\\Parser\\LogParser');

        $chats = [];

        // process all found chat entries
        foreach ($dbChats as $chat) {
            /** @var Chat $chat */
            if ($chat->getNotice() && $chat->getNoticeTarget() !== 'NORMAL') {
                continue;
            }

            $chats[] = $this->transformToChatMessage($chat);
        }

        if (in_array('application/json', $request->getAcceptableContentTypes())) {
            return new JsonResponse($chats);
        }

        // time to render!
        $response = $this->render('KorobiWebBundle:controller/generic/irc/channel:logs.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'network_slug' => $dbNetwork->getSlug(),
            'channel_name' => $dbChannel->getChannel(),
            'channel_slug' => $channel,
            'logs' => $chats,
            'log_date_formatted' => date('F j, Y', mktime(0, 0, 0, $month, $day, $year)),
            'log_date' => date('Y/m/d', mktime(0, 0, 0, $month, $day, $year)),
            'is_tail' => $tail !== false,
            'showing_current' => $showingCurrent,
            'first_for_channel' => $repo->findFirstByChannel($dbNetwork->getSlug(), $dbChannel->getChannel())->toArray(false)[0]->getDate()->format('Y/m/d'),
            'available_log_days' => $this->grabAvailableLogDays($dbNetwork->getSlug(), $dbChannel->getChannel()),
        ]);

        if (count($chats) == 0) {
            $response->setStatusCode(404);
        }

        return $response;
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
            $month = date('m');
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
            'message'    => $this->parseChatMessage($chat),
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
     * @return array An array of available unique days.
     */
    private function grabAvailableLogDays($network, $channel) {
        return array_map(function(ChatIndex $item) {
            return [
                'year' => $item->getYear(),
                'day_of_year' => $item->getDayOfYear(),
                'has_valid_content' => $item->getHasValidContent(),
            ];
        }, $this
                ->get('doctrine_mongodb')
                ->getManager()
                ->getRepository('KorobiWebBundle:ChatIndex')
                ->findAllByChannel($network, $channel)
                ->toArray(false)
        );
    }
}
