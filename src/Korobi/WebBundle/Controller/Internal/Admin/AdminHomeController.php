<?php

namespace Korobi\WebBundle\Controller\Internal\Admin;

use Korobi\WebBundle\Controller\BaseController;

class AdminHomeController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        return $this->render('KorobiWebBundle:controller/internal/admin:home.html.twig');
    }
}
