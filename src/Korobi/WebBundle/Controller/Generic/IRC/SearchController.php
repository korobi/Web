<?php

namespace Korobi\WebBundle\Controller\Generic\IRC;

use Korobi\WebBundle\Controller\BaseController;
use Korobi\WebBundle\Repository\ChatRepository;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;

class SearchController extends BaseController {

    public function searchAction(Request $request) {
        $term = $request->get("term");
        $page = (int) $request->get("page", 1);
        if ($page < 1) $page = 1;

        dump($this->getChatRepository()->findAllBySearchTerm($term, $page)->toArray());
    }

    /**
     * @return ChatRepository
     */
    private function getChatRepository() {
        return $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Chat');
    }
}
