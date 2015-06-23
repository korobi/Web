<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;

class DocsController extends BaseController {

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function renderAction($file) {
        if ($file === 'commands') {
            return $this->redirect('https://docs.korobi.io/channel/commands/index.html', 301);
        }

        return $this->redirect('https://docs.korobi.io/', 301);
    }
}
