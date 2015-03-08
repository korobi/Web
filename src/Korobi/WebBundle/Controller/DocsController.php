<?php

namespace Korobi\WebBundle\Controller;


use Korobi\WebBundle\Exception\NotImplementedException;
use Michelf\Markdown;
use Symfony\Component\HttpFoundation\Response;

class DocsController extends BaseController {

    public function renderAction($file) {
        if (!preg_match("/^[A-Za-z0-9_]+$/", $file)) {
            throw $this->createNotFoundException("Invalid doc");
        }

        $fn = $this->get('kernel')->getRootDir() . "/../docs/" . $file . ".md";
        if (!file_exists($fn)) {
            throw $this->createNotFoundException("Doc does not exist, " . $fn);
        }

        $parser = new Markdown;
        $parser->no_entities = true;
        $parser->no_markup = true;

        return new Response($parser->transform(file_get_contents($fn)));
    }
}
