<?php

namespace Korobi\WebBundle\Handler;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Http\Logout\LogoutSuccessHandlerInterface;

class LogoutSuccessHandler implements LogoutSuccessHandlerInterface {

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param RouterInterface $router
     */
    public function __construct(RouterInterface $router) {
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function onLogoutSuccess(Request $request) {
        // redirect the user to where they were before the login process begun.
        $backUrl = $request->get("_destination");

        try {
            $this->router->match($backUrl);
            return new RedirectResponse($backUrl);
        } catch (MethodNotAllowedException $e) {
            return new RedirectResponse('/');
        } catch (ResourceNotFoundException $e) {
            return new RedirectResponse('/');
        }
    }
}
