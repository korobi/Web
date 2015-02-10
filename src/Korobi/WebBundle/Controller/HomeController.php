<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Repository\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;

class HomeController extends BaseController {

    /**
     * @var ChannelRepository The channel repository.
     */
    private $channels;

    /**
     * @var EngineInterface
     */
    private $templating;

    public function __construct(EngineInterface $templating, ChannelRepository $channels) {
        $this->channels = $channels;
        $this->templating = $templating;
    }

    public function homeAction() {
        dump($this->channels->findAllByNetwork('solas')->toArray());
        return $this->templating->renderResponse('KorobiWebBundle::home.html.twig', ["debug"]);
    }
}
