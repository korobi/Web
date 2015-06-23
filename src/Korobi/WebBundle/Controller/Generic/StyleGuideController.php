<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;

class StyleGuideController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayAction() {
        return $this->render('KorobiWebBundle:controller/generic:style_guide.html.twig');
    }
}
