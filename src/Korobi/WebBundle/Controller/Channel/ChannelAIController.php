<?php

namespace Korobi\WebBundle\Controller\Channel;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\ChannelAI;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Parser\IRCTextParser;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\Request;

class ChannelAIController extends BaseController {

    /**
     * @Route("/channel/{network}/{channel}/ai/",name = "channel_ai")
     *
     * @param Request $request
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function listAction(Request $request, $network, $channel) {
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

        // fetch document
        /** @var ChannelAI $dbChannelAI */
        $dbChannelAI = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:ChannelAI')
            ->findByChannel($dbNetwork->getSlug(), $dbChannel->getChannel())
            ->toArray(false);

        // make sure we actually have a document
        if (empty($dbChannelAI)) {
            throw $this->createNotFoundException('Could not find AI data for channel');
        }

        // grab first slice
        $dbChannelAI = $dbChannelAI[0];

        $patterns = [];
        foreach ($dbChannelAI->getPatterns() as $dbPattern) {
            $pattern = array_merge([
                // not all patterns have the same fields. if we try and render non-existent fields within the view, twig
                // will throw an exception. instead, provide some default values for it to use instead
                'case_insensitive' => false,
                'user_mode' => 'none',
                'exemptions' => []
            ], $dbPattern);
            $pattern['case_insensitive'] = $pattern['case_insensitive'] ? 'true' : 'false';
            $patterns[] = $pattern;
        }


        // time to render!
        return $this->render('KorobiWebBundle:controller/channel:ai.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'join_message_enabled' => $dbChannelAI->getJoinMessageEnabled(),
            'join_message' => IRCTextParser::parse($dbChannelAI->getJoinMessage()),
            'patterns' => $patterns
        ]);
    }
}
