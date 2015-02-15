<?php

namespace Korobi\WebBundle\Controller;

use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ThemeController extends BaseController {

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    /**
     * @param Request $request
     * @return RedirectResponse
     */
    public function toggleAction(Request $request) {
        if ($this->session->has('theme-light')) {
            $this->session->remove('theme-light');
        } else {
            $this->session->set('theme-light', true);
        }

        return $this->redirect($request->headers->get('referer'));
    }
}
