<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\ChatIndex;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Repository\ChatRepository;
use Korobi\WebBundle\Util\FileCache;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class ChannelLogController extends BaseController {

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

        if(!$dbChannel->getLogsEnabled() && !$this->authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createNotFoundException(); // TODO
        }

        // populate variables with request information if available, or defaults
        // note: validation is done here
        /** @var $date \DateTime */
        list($date, $showingToday, $tail) = self::populateRequest($year, $month, $day, $tail);

        $cache = $this->getCache();
        $cacheKey = $this->generateCacheKey($dbNetwork, $dbChannel, $date);

        if(!$showingToday && $cache->exists($cacheKey)) {
            $logData = $cache->get($cacheKey);
        } else {
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
                    new \MongoDate($date->modify('+1 day')->getTimestamp())
                )
                    ->toArray();
            } else {
                $dbChats = $repo->findAllByChannelAndDate(
                    $network,
                    $dbChannel->getChannel(),
                    new \MongoDate($date->getTimestamp()),
                    new \MongoDate($date->modify('+1 day')->getTimestamp())
                )
                    ->toArray();
            }

            // if a tail is requested and no last id was provided...
            if ($tail !== false && $last_id === false) {
                // ... grab the last X chats
                $dbChats = array_slice($dbChats, -$tail);
            }

            // get the data back for the twig frontend to render, from our chats in the database
            $chats = $this->getRenderManager()->renderLogs($dbChats);

            if (in_array('application/json', $request->getAcceptableContentTypes())) {
                return new JsonResponse(array_map(function($line) {
                    $line['timestamp'] = $line['timestamp']->getTimestamp();
                    return $line;
                }, $chats));
            }

            $firstChannelEvent = $this->getFirstChannelEvent($repo, $dbNetwork, $dbChannel);
            $logData = [
                'network_name' => $dbNetwork->getName(),
                'network_slug' => $dbNetwork->getSlug(),
                'channel_name' => $dbChannel->getChannel(),
                'channel_slug' => $channel,
                'logs' => $chats,
                'date' => $date,
                'is_tail' => $tail !== false,
                'showing_today' => $showingToday,
                'first_for_channel' => $firstChannelEvent->getDate()->format('Y/m/d'),
                'showing_first_day' => $firstChannelEvent->getDate()->setTime(0, 0, 0) == $date,
            ];

            // Do not cache logs if we are rendering the current date's logs.
            if(!$showingToday) {
                $cache->set($cacheKey, $logData);
            }
        }

        // Grab the topic while it's fresh
        $topic = null;
        $dbTopic = $dbChannel->getTopic();
        if($dbTopic) { // TODO: Extract to RenderManager
            $topic = [
                'value' => $dbTopic['value'],
                'time' => $dbTopic['time']->toDateTime(),
                'setter_nick' => $this->get("korobi.irc.log_parser")->transformActor($dbTopic['actor_nick']),
            ];
        }
        $logData['topic'] = $topic;

        // key should not be cached
        $logData['channel_private'] = $dbChannel->isPrivate();

        $logData['available_log_days'] = $this->grabAvailableLogDays($dbNetwork->getSlug(), $dbChannel->getChannel());

        // time to render!
        $response = $this->render('KorobiWebBundle:controller/generic/irc/channel:logs.html.twig', $logData);

        if (count($logData['logs']) == 0) {
            $response->setStatusCode(404);
        }

        return $response;
    }

    /**
     * @return FileCache
     */
    private function getCache() {
        return new FileCache($this->getParameter('korobi.config')['log_cache_directory']);
    }

    /**
     * @param Network $network
     * @param Channel $channel
     * @param \DateTimeInterface $date
     * @return array
     */
    private function generateCacheKey(Network $network, Channel $channel, \DateTimeInterface $date) {
        return [$network->getSlug(), $channel->getChannel(), $date->format('Y-z')];
    }

    /**
     * @param $year
     * @param $month
     * @param $day
     * @param $tail
     * @return array
     */
    private function populateRequest($year, $month, $day, $tail) {
        $today = (new \DateTime('now', new \DateTimeZone('UTC')))->setTime(0, 0, 0);
        $timestamp = $today->getTimestamp();

        if (!$year) {
            $year = gmdate('Y', $timestamp);
        }

        if (!$month) {
            $month = gmdate('m', $timestamp);
        }

        if (!$day) {
            $day = gmdate('d', $timestamp);
        }

        if ($tail !== false) {
            // maximum: 90  |  minimum: 5
            if ($tail > 90 || $tail < 5) {
                // fallback to 30
                $tail = 30;
            }
        }

        $date = (new \DateTimeImmutable('now', new \DateTimeZone('UTC')))
            ->setTime(0, 0, 0)
            ->setDate($year, $month, $day);

        return [$date, $date == $today, $tail];
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

    /**
     * Grabs the first available chat entry for a given channel/network combination.
     *
     * @param ChatRepository $repo
     * @param Network $dbNetwork
     * @param Channel $dbChannel
     * @return Chat
     */
    private function getFirstChannelEvent($repo, $dbNetwork, $dbChannel) {
        return $repo->findFirstByChannel($dbNetwork->getSlug(), $dbChannel->getChannel())->toArray(false)[0];
    }
}
