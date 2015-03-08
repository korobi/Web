<?php

namespace Korobi\WebBundle\Controller;


use Korobi\WebBundle\Exception\NotImplementedException;
use Michelf\Markdown;

class DocsController extends BaseController {

    public function renderAction($file) {
        if (!preg_match("/^[A-Za-z0-9_]+$/", $file)) {
            throw $this->createAccessDeniedException("Invalid doc");
        }

        $fn = $this->get('kernel')->getRootDir() . "/docs/" . $file . ".md";
        if (!file_exists($fn)) {
            throw $this->createAccessDeniedException("Doc does not exist");
        }

        $parser = new Markdown;
        $parser->no_entities = true;
        $parser->no_markup = true;

        return $parser->transform(file_get_contents($fn));
    }
}
