<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;
use Symfony\Component\HttpFoundation\Response;

class DynamicController extends BaseController {

    /**
     * @return Response
     */
    public function analyticsScriptSource() {
        $resp = $this->render('KorobiWebBundle:partial:analytics.js.twig');
        $resp->headers->set('Content-Type', 'application/javascript');
        return $resp;
    }
}
