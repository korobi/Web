<?php

namespace Korobi\WebBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutHandler implements LogoutSuccessHandlerInterface {

    public function onLogoutSuccess(Request $request) {
        // redirect the user to where they were before the login process begun.
        $backUrl = $request->get("_destination");
        if (substr($backUrl, 0, 1) === "/") {
            $backUrl = substr($backUrl, 1);
        }
        $response = new RedirectResponse($request->getBaseUrl() . $backUrl);
        return $response;
    }

}
