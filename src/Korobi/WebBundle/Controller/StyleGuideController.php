<?php

namespace Korobi\WebBundle\Controller;

class StyleGuideController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayAction() {
        return $this->render('KorobiWebBundle::style_guide.html.twig');
    }
}
