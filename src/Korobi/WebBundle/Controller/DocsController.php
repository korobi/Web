<?php

namespace Korobi\WebBundle\Controller;

class DocsController extends BaseController {

    const BASE_HREF = '/docs/';

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function renderAction($file) {
        return $this->redirect('https://docs.korobi.io/');
    }
}
