<?php

namespace Korobi\WebBundle\Handler;

use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Routing\Exception\MethodNotAllowedException;
use Symfony\Component\Routing\Exception\ResourceNotFoundException;
use Symfony\Component\Routing\RouterInterface;
use Symfony\Component\Security\Core\Authentication\Token\TokenInterface;
use Symfony\Component\Security\Http\Authentication\DefaultAuthenticationSuccessHandler;
use Symfony\Component\Security\Http\HttpUtils;

class AuthenticationSuccessHandler extends DefaultAuthenticationSuccessHandler {

    /**
     * @var RouterInterface
     */
    private $router;

    /**
     * @param HttpUtils $httpUtils
     * @param RouterInterface $router
     */
    public function __construct(HttpUtils $httpUtils, RouterInterface $router) {
        parent::__construct($httpUtils, []);
        $this->router = $router;
    }

    /**
     * @param Request $request
     * @param TokenInterface $token
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function onAuthenticationSuccess(Request $request, TokenInterface $token) {
        $target = $this->determineTargetUrl($request);

        try {
            $this->router->match($target);
            return $this->httpUtils->createRedirectResponse($request, $target);
        } catch (MethodNotAllowedException $e) {
            return $this->httpUtils->createRedirectResponse($request, '/');
        } catch (ResourceNotFoundException $e) {
            return $this->httpUtils->createRedirectResponse($request, '/');
        }
    }
}
