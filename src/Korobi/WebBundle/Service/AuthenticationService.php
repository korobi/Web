<?php

namespace Korobi\WebBundle\Service;

use Korobi\WebBundle\Document\Channel;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthenticationService implements IAuthenticationService {

    /**
     * @var AuthorizationCheckerInterface
     */
    private $authChecker;

    /**
     * AuthenticationService constructor.
     * @param AuthorizationCheckerInterface $authChecker
     */
    public function __construct(AuthorizationCheckerInterface $authChecker) {
        $this->authChecker = $authChecker;
    }

    /**
     * @param Channel $dbChannel
     * @param Request $request
     * @return bool
     */
    public function hasAccessToChannel(Channel $dbChannel, Request $request) {
        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if (($key === null || $key !== $dbChannel->getKey()) && !$this->authChecker->isGranted('ROLE_SUPER_ADMIN')) {
                return false;
            }
        }
        return true;
    }
}
