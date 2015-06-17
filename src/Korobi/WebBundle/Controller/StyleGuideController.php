<?php

namespace Korobi\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class StyleGuideController extends BaseController {

    /**
     * @Route("/style-guide/", name = "style_guide")
     *
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function displayAction() {
        return $this->render('KorobiWebBundle::style_guide.html.twig');
    }
}
