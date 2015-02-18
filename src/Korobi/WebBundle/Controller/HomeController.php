<?php

namespace Korobi\WebBundle\Controller;

class HomeController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        return $this->render('KorobiWebBundle::home.html.twig');
    }
}
