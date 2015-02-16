<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;

class LogController extends BaseController {

    public function homeAction() {

        $rawNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray();

        $networks = [];
        foreach ($rawNetworks as $network) {
            /** @var $network Network  */
            $name = $network->getSlug();
            if (!in_array($name, $networks)) {
                $networks[$name] = [
                    'name' => $network->getName(),
                    'href' => $this->generateUrl('logs_network', [
                        'network' => $name
                    ])
                ];
            }
        }

        return $this->render('KorobiWebBundle:controller/log:home.html.twig', [
            'networks' => $networks
        ]);
    }

    // TODO:
    // - invalid network name
    // - empty network
    public function networkAction($network) {
        $rawChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findAllByNetwork($network)
            ->toArray();
        $channels = [];

        foreach ($rawChannels as $channel) {
            /** @var $channel Channel  */
            $channels[] = [
                'name' => $channel->getChannel(),
                'href' => $this->generateUrl('logs_channel', [
                    'network' => $network,
                    'channel' => self::transformChannelName($channel->getChannel())
                ])
            ];
        }

        return $this->render('KorobiWebBundle:controller/log:network.html.twig', [
            'channels' => $channels
        ]);
    }

    // TODO:
    // - invalid channel
    // - tail
    // - security key
    public function channelAction($network, $channel) {
        $logs = [];

        $rawLogs = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat')
            ->findAllByChannelAndDate(
                $network,
                '#' . $channel, // TODO
                new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, date('n'), date('d'))))),
                new \MongoDate(strtotime(date('Y-m-d\TH:i:s.000\Z', mktime(0, 0, 0, date('n'), date('d') + 1))))
            )
            ->toArray();

        foreach ($rawLogs as $chat) {
            /** @var $chat Chat  */

            switch ($chat->getType()) {
                case 'ACTION':
                    break;
                case 'JOIN':
                    break;
                case 'KICK':
                    break;
                case 'MESSAGE':
                    $logs[] = $this->parseMessage($chat);
                    break;
                case 'MODE':
                    $logs[] = $this->parseMode($chat);
                    break;
                case 'NICK':
                    break;
                case 'PART':
                    break;
                case 'QUIT':
                    break;
                case 'TOPIC':
                    break;
            }

        }

        return $this->render('KorobiWebBundle:controller/log:channel.html.twig', [
            'logs' => $logs
        ]);
    }

    private function parseMessage(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '<';
        switch ($chat->getActorPrefix()) {
            case 'OWNER':
                $result .= '~';
                break;
            case 'ADMIN':
                $result .= '&';
                break;
            case 'OPERATOR':
                $result .= '@';
                break;
            case 'HALF_OP':
                $result .= '%';
                break;
            case 'VOICE':
                $result .= '+';
                break;
            case 'NORMAL':
                break;
        }
        $result .= self::transformActor($chat->getActorName());
        $result .= '> ';

        // message
        $result .= IRCTextParser::parse($chat->getMessage());

        return $result;
    }

    private function parseMode(Chat $chat) {
        $result = '';

        /** @var $date \DateTime */
        $date = $chat->getDate();
        $result .= '[' . date('H:i:s', $date->getTimestamp()) . '] '; // time

        $result .= '* ';
        switch ($chat->getActorPrefix()) {
            case 'OWNER':
                $result .= '~';
                break;
            case 'ADMIN':
                $result .= '&';
                break;
            case 'OPERATOR':
                $result .= '@';
                break;
            case 'HALF_OP':
                $result .= '%';
                break;
            case 'VOICE':
                $result .= '+';
                break;
            case 'NORMAL':
                break;
        }
        $result .= self::transformActor($chat->getActorName());
        $result .= ' sets mode ' . $chat->getMessage();

        return $result;
    }
}
