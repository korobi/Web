<?php

namespace Korobi\WebBundle\Controller;

use Github\Client;

class RevisionsController extends BaseController {

    private static $repositories = ['Korobi', 'Web', 'Felix'];

    /**
     * @param $repository
     * @return \Symfony\Component\HttpFoundation\RedirectResponse|\Symfony\Component\HttpFoundation\Response
     */
    public function listAction($repository) {
        // only allow viewing of certain repositories
        if (!in_array($repository, self::$repositories)) {
            return $this->redirectToRoute('revisions');
        }

        $client = new Client();
        $client->authenticate($this->container->getParameter('github_oauth_token'), Client::AUTH_HTTP_TOKEN);

        return $this->render('KorobiWebBundle::revisions.html.twig', [
            'repository' => $repository,
            'revisions' => $client
                ->api('repo')
                ->commits()
                ->all('korobi', $repository, ['sha' => 'master'])
        ]);
    }
}
