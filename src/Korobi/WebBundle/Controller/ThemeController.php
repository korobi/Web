<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Repository\ChannelRepository;
use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Symfony\Bundle\FrameworkBundle\Templating\EngineInterface;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\RedirectResponse;
use Symfony\Component\HttpFoundation\Session\Session;

class ThemeController extends BaseController {

    /**
     * @var Session
     */
    private $session;

    /**
     * @param Session $session
     */
    public function __construct(Session $session) {
        $this->session = $session;
    }

    public function toggleAction() {
        if ($this->session->has("light-theme")) {
            $this->session->remove("light-theme");
        } else {
            $this->session->set("light-theme", true);
        }
        return RedirectResponse::create("/");
    }
}
