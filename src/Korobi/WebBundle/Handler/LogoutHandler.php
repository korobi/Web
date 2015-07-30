<?php

namespace Korobi\WebBundle\Handler;

use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\RedirectResponse;

class LogoutHandler implements LogoutSuccessHandlerInterface {

    public function onLogoutSuccess(Request $request) {
        // redirect the user to where they were before the login process begun.
        $backUrl = $request->get("_destination");

        $response = new RedirectResponse($backUrl);
        return $response;
    }

}
