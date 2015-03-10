<?php

namespace Korobi\WebBundle\Controller;

use Michelf\Markdown;

class DocsController extends BaseController {

    public function renderAction($file) {
        if (!preg_match("/^[A-Za-z0-9_]+$/", $file)) {
            throw $this->createNotFoundException("Invalid doc");
        }

        $fn = $this->get('kernel')->getRootDir() . "/../docs/" . $file . ".md";
        if (!file_exists($fn) || $file === "README") {
            throw $this->createNotFoundException("Doc does not exist, " . $fn);
        }

        $parser = new Markdown;
        $parser->no_entities = true;
        $parser->no_markup = true; // not bulletproof but CSP will fix whatever else we get

        $content = $this->render('KorobiWebBundle::docs.html.twig', ["content" => $parser->transform(file_get_contents($fn))]);
        return $content;
    }

    public function indexAction() {
        return $this->renderAction("index");
    }
}
