<?php

namespace Korobi\WebBundle\Controller;

use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;

class DocsController extends BaseController {

    /**
     * @Route("/docs/", name = "docs")
     * @Route("/docs/{file}/", name = "docs_file")
     *
     * @param $file
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function renderAction($file = 'index') {
        return $this->redirect('https://docs.korobi.io/');
    }
}
