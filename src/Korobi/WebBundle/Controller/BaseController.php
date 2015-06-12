<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class BaseController extends Controller {

    /**
     * @var AuthorizationChecker
     */
    protected $authChecker;

    /**
     * @param AuthorizationChecker $authChecker
     */
    public function setAuthChecker(AuthorizationChecker $authChecker) {
        $this->authChecker = $authChecker;
    }

    /**
     * @param Request $request
     * @return array|mixed
     */
    protected function getJsonRequestData(Request $request) {
        $data = [];

        $content = $request->getContent();
        if (!empty($content)) {
            $data = json_decode($content, true);
        }

        return $data;
    }

    /**
     * Transform a channel name.
     *
     * @param $channel
     * @param bool $reverse
     * @return mixed
     */
    protected static function transformChannelName($channel, $reverse = false) {
        if (!$reverse) {
            // change for double (and above) #
            $length = strlen('##');
            if (substr($channel, 0, $length) === '##') {
                return $channel;
            }

            // remove for single #
            return str_replace('#', '', $channel);
        } else {
            // change for double (and above) %23
            $length = strlen('#');
            if (substr($channel, 0, $length) === '#') {
                return $channel;
            }

            // add single #
            return '#' . $channel;
        }
    }

    /**
     * @param $network
     * @param $channel
     * @return array
     */
    protected function createNetworkChannelPair($network, $channel) {
        // validate network
        /** @var Network $dbNetwork */
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
        /** @var Channel $dbChannel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, self::transformChannelName(preg_quote($channel), true)) // TODO
            ->toArray(false);

        // make sure we actually have a channel
        if (empty($dbChannel)) {
            throw $this->createNotFoundException('Could not find channel');
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

        return [$dbNetwork, $dbChannel];
    }
}
