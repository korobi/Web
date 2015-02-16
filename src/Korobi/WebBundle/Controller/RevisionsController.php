<?php

namespace Korobi\WebBundle\Controller;

class RevisionsController extends BaseController {

    public function listAction() {
        return $this->render('KorobiWebBundle::revisions.html.twig');
    }
}
