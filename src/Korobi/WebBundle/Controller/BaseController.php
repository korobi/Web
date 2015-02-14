<?php


namespace Korobi\WebBundle\Controller;


use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

abstract class BaseController {

    /**
     * @var AuthorizationChecker
     */
    protected $authorisationChecker;

    protected function getJsonRequestData(Request $request) {
        $params = [];
        $content = $request->getContent();
        if (!empty($content)) {
            $params = json_decode($content, true);
        }
        return $params;
    }

    public function setAuthorisationChecker(AuthorizationChecker $checker) {
        $this->authorisationChecker = $checker;
    }


}
