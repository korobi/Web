<?php

namespace Korobi\WebBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutHandler implements LogoutSuccessHandlerInterface {

    public function onLogoutSuccess(Request $request) {
        // redirect the user to where they were before the login process begun.
        $backUrl = $request->get("_destination");

        $response = new RedirectResponse($backUrl);
        return $response;
    }

}
