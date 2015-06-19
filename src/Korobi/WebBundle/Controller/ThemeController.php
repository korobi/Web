<?php

namespace Korobi\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Session\Session;

class ThemeController extends BaseController {

    /**
     * @Route("/theme/", name = "theme_toggle")
     *
     * @param Request $request
     * @return RedirectResponse
     */
    public function toggleAction(Request $request) {
        /** @var Session $session */
        $session = $this->get('session');
        if ($session->has('theme-light')) {
            $session->remove('theme-light');
        } else {
            $session->set('theme-light', true);
        }

        return $this->redirect($request->headers->get('referer', '/'));
    }
}
