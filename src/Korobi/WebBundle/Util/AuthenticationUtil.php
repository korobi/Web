<?php

namespace Korobi\WebBundle\Util;

use Korobi\WebBundle\Document\Channel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthenticationUtil {

    /**
     * @param Channel $dbChannel
     * @param Request $request
     * @param AuthorizationCheckerInterface $authChecker
     * @throws \Exception
     */
    public static function checkKeyAccess(Channel $dbChannel, Request $request, AuthorizationCheckerInterface $authChecker) {
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if (($key === null || $key !== $dbChannel->getKey()) && !$authChecker->isGranted('ROLE_SUPER_ADMIN')) {
                throw new \Exception('Unauthorized'); // TODO
            }
        }
    }
}
