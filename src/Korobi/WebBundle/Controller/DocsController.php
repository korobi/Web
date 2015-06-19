<?php

namespace Korobi\WebBundle\Controller;

class DocsController extends BaseController {

    /**
     * @param $file
     * @return \Symfony\Component\HttpFoundation\RedirectResponse
     */
    public function renderAction($file = 'index') {
        if ($file === 'commands') {
            return $this->redirect('https://docs.korobi.io/channel/commands/index.html', 301);
        }

        return $this->redirect('https://docs.korobi.io/', 301);
    }
}
