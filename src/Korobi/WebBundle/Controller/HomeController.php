<?php

namespace Korobi\WebBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;

class HomeController extends Controller {

    public function homeAction() {
        return $this->render('KorobiWebBundle::home.html.twig');
    }
}
