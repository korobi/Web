<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Parser\Markdown;
use Korobi\WebBundle\Util\StringUtil;

class DocsController extends BaseController {
    const BASE_HREF = '/docs/';

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function renderAction($file) {
        if (StringUtil::endsWith($file, ".md", true)) {
            $file = substr($file, 0, -3);
        }

        if (!preg_match("/^[A-Za-z0-9_]+$/", $file)) {
            throw $this->createNotFoundException("Invalid doc");
        }

        $fn = $this->get('kernel')->getRootDir() . "/../docs/" . $file . ".md";

        if (!file_exists($fn) || $file === "README") {
            throw $this->createNotFoundException("Doc does not exist, " . $fn);
        }

        $parser = new Markdown;
        $parser->no_entities = true;
        $parser->no_markup = true; // not bulletproof but CSP and PRs will fix whatever else we get

        $viewData = ["pageName" => $file, "content" => $parser->transform(file_get_contents($fn))];
        $content = $this->render('KorobiWebBundle::docs.html.twig', $viewData);
        return $content;
    }
}
