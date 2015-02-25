<?php

namespace Korobi\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class BaseController extends Controller {

    /**
     * @var AuthorizationChecker
     */
    protected $authChecker;

    /**
     * @param AuthorizationChecker $checker
     */
    public function setAuthChecker(AuthorizationChecker $checker) {
        $this->authChecker = $checker;
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
            $length = strlen('#');
            if (substr($channel, 0, $length) === '#') {
                return str_replace('#', '%23', $channel);
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
}
