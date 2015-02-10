<?php


namespace Korobi\WebBundle\Controller;


use Symfony\Component\HttpFoundation\Request;

abstract class BaseController {

    protected function getJsonRequestData(Request $request) {
        $params = [];
        $content = $request->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true);
        }
        return $params;
    }

}
