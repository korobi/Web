<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Chat;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class BaseController extends Controller {

    /**
     * @var AuthorizationChecker
     */
    protected $authChecker;

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
     * @param AuthorizationChecker $checker
     */
    public function setAuthChecker(AuthorizationChecker $checker) {
        $this->authChecker = $checker;
    }

    /**
     * Transform a channel name.
     *
     * @param $channel
     * @return mixed
     */
    protected static function transformChannelName($channel) {
        // change for double (and above) #
        $length = strlen('##');
        if (substr($channel, 0, $length) === '##') {
            return str_replace('#', '', $channel);
        }

        // remove for single #
        return str_replace('#', '', $channel);
    }

    /**
     * Transform an actor name.
     *
     * @param $actor
     * @return string
     */
    protected static function transformActor($actor) {
        if ($actor == Chat::ACTOR_INTERNAL) {
            return 'Server';
        }

        return $actor;
    }
}
