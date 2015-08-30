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
        if ($this->authChecker->isGranted('ROLE_PRIVATE_ACCESS')) {
            return IAuthenticationService::ALLOW;
        }

        if ($dbChannel->getKey() !== null) {
            $key = $request->query->get('key');
            if ($key === null) {
                return IAuthenticationService::REJECT;
            }
            if ($key !== $dbChannel->getKey()) {
                return IAuthenticationService::INVALID_KEY;
            }
        }
        return IAuthenticationService::ALLOW;
    }
}
