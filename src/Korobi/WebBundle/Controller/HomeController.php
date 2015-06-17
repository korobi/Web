<?php

namespace Korobi\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class HomeController extends BaseController {

    /**
     * @Route("/", name = "home")
     * @Route("/dummy/", name = "dummy")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        return $this->render('KorobiWebBundle::home.html.twig');
    }
}
