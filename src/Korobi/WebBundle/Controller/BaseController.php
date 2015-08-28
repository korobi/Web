<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Exception\ChannelAccessException;
use Korobi\WebBundle\IRC\Log\Render\RenderManager;
use Korobi\WebBundle\Service\IAuthenticationService;
use Psr\Log\LoggerInterface;
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
    // todo move this
    public static function transformChannelName($channel, $reverse = false) {
        if (!$reverse) {
            // Do nothing if the channel name starts with two number signs ('##').
            if (substr($channel, 0, 2) === '##') {
                return $channel;
            }

            // Remove single '#' from the channel name.
            return str_replace('#', '', $channel);
        } else {
            // Assume that any channels provided that start with a single number sign ('#') do not require any changes.
            if (substr($channel, 0, 1) === '#') {
                return $channel;
            }

            // There was no number sign in front of the channel name, so we add one.
            return '#' . $channel;
        }
    }

    /**
     * @param $network
     * @param $channel
     * @return array
     * @throws ChannelAccessException If the user doesn't have access to the channel.
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
            ->findByChannel($network, self::transformChannelName($channel, true))
            ->toArray(false);

        // make sure we actually have a channel
        if (empty($dbChannel)) {
            throw $this->createNotFoundException('Could not find channel');
        }

        // ensure the user is appropriately authenticated
        if (!$this->getAuthenticationService()->hasAccessToChannel($dbChannel, $this->getRequestNotDeprecated())) {
            throw new ChannelAccessException($dbNetwork->getName(), $dbChannel->getChannel());
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

        return [$dbNetwork, $dbChannel];
    }

    /**
     * @return LoggerInterface
     */
    protected function getLogger() {
        return $this->get('logger');
    }

    /**
     * @return IAuthenticationService
     */
    protected function getAuthenticationService() {
        return $this->get("korobi.authentication_service");
    }

    /**
     * @return RenderManager
     */
    protected function getRenderManager() {
        return $this->get("korobi.irc.render_manager");
    }

    /**
     * Future-proof past Symfony 3!
     */
    protected function getRequestNotDeprecated() {
        // I'll understand if you think this is terrible, I'm inclined to agree
        // Trouble is, if we inject Request via the constructor, all the other controllers
        // then need to use DI to grab yet another dependency via the constructor.
        return $this->container->get('request_stack')->getCurrentRequest();
    }
}
