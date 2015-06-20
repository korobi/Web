<?php

namespace Korobi\WebBundle\Controller;

class HomeController extends BaseController {

    /**
     * @return \Symfony\Component\HttpFoundation\Response
     */
    public function homeAction() {
        $dbChannels = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->getRecentlyActiveChannels(10)
            ->toArray();

        return $this->render('KorobiWebBundle::home.html.twig', ["channels" => $dbChannels]);
    }
}
