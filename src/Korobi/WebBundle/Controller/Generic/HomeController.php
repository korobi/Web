<?php

namespace Korobi\WebBundle\Controller\Generic;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Document\Network;

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
        $dbNetworks = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetworks()
            ->toArray(false);

        $networks = [];
        /** @var Network $dbNetwork */
        foreach($dbNetworks as $dbNetwork) {
            $networks[$dbNetwork->getSlug()] = $dbNetwork->getName();
        }

        return $this->render('KorobiWebBundle:controller/generic:home.html.twig', [
            'now' => time(),
            'channels' => $dbChannels,
            'networks' => $networks,
        ]);
    }
}
