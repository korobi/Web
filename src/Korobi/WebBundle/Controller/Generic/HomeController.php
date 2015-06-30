<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;

class HomeController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        $dbChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->getRecentlyActiveChannels(5)
            ->toArray();

        return $this->render('KorobiWebBundle:controller/generic:home_old.html.twig', [
            'now' => time(),
            'channels' => $dbChannels,
        ]);
    }
}
