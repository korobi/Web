<?php

namespace Korobi\WebBundle\Controller;

class HomeController extends BaseController {

    public function homeAction() {
        return $this->render('KorobiWebBundle::home.html.twig');
    }
}
