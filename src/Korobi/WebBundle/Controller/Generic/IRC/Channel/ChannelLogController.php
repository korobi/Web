<?php

namespace Korobi\WebBundle\Controller\Generic\IRC\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
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

        // check if this channel requires a key
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if ($key === null || $key !== $dbChannel->getKey()) {
                throw new \Exception('Unauthorized'); // TODO
            }
        } else if(!$dbChannel->getLogsEnabled() && !$this->authChecker->isGranted('ROLE_SUPER_ADMIN')) {
            throw $this->createNotFoundException();
        }

        // populate variables with request information if available, or defaults
        // note: validation is done here
        list($date, $tail) = self::populateRequest($year, $month, $day, $tail);
        $now = new \DateTime();
        $showingToday = $date->getTimestamp() - $now->setTime(0, 0, 0)->getTimestamp() == 0;

        $cache = $this->getCache();
        $cacheKey = $this->generateCacheKey($dbNetwork, $dbChannel, $date);

        if(!$showingToday && $cache->exists($cacheKey)) {
            $params = $cache->get($cacheKey);

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
                return new JsonResponse($chats);
            }

            $topic = null;
            $dbTopic = $dbChannel->getTopic();
            if($dbTopic) {
                $topic = [
                    'value' => $dbTopic['value'],
                    'setter_nick' => $this->get("korobi.irc.log_parser")->transformActor($dbTopic['actor_nick']),
                ];
            }

            $params = [
                'network_name' => $dbNetwork->getName(),
                'network_slug' => $dbNetwork->getSlug(),
                'channel_name' => $dbChannel->getChannel(),
                'channel_slug' => $channel,
                'topic' => $topic,
                'logs' => $chats,
                'log_date_formatted' => $date->format('F j, Y'),
                'log_date' => $date->format('Y/m/d'),
                'is_tail' => $tail !== false,
                'showing_today' => $showingToday,
                'first_for_channel' => $repo->findFirstByChannel($dbNetwork->getSlug(), $dbChannel->getChannel())->toArray(false)[0]->getDate()->format('Y/m/d'),
                'available_log_days' => $this->grabAvailableLogDays($dbNetwork->getSlug(), $dbChannel->getChannel()),
            ];
            $cache->set($cacheKey, $params);
        }

        // time to render!
        $response = $this->render('KorobiWebBundle:controller/generic/irc/channel:logs.html.twig', $params);

        if (count($params['logs']) == 0) {
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

    private function generateCacheKey(Network $network, Channel $channel, \DateTimeInterface $date) {
        return [$network->getSlug(), $channel->getChannel(), $date->format("Y-z")];
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

        $date = new \DateTimeImmutable();
        return [$date->setTime(0, 0, 0)->setDate($year, $month, $day), $tail];
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
