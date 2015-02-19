<?php

namespace Korobi\WebBundle\Controller;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Document\Network;
use Korobi\WebBundle\Document\User;
use Symfony\Component\HttpFoundation\Request;

class ChannelAdminController extends BaseController {

    /**
     * @param Request $request
     * @param $network
     * @param $channel
     * @return \Symfony\Component\HttpFoundation\Response
     * @throws \Exception
     */
    public function homeAction(Request $request, $network, $channel) {
        // validate network
        /** @var $dbNetwork Network */
        $dbNetwork = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Network')
            ->findNetwork($network)
            ->toArray(false);

        // make sure we actually have a network
        if (empty($dbNetwork)) {
            throw new \Exception('Could not find network'); // TODO
        }

        // grab first slice
        $dbNetwork = $dbNetwork[0];

        // fetch channel
        /** @var $dbChannel Channel */
        $dbChannel = $this->get('doctrine_mongodb')
            ->getManager()
            ->getRepository('KorobiWebBundle:Channel')
            ->findByChannel($network, self::transformChannelName($channel, true))
            ->toArray(false);

        // make sure we actually have a channel
        if (empty($dbChannel)) {
            throw new \Exception('Could not find channel'); // TODO
        }

        // grab first slice
        $dbChannel = $dbChannel[0];

        // check if authorized
        /** @var $user User */
        $user = $this->getUser();
        if ($user === null || (!in_array($user->getGitHubUserId(), $dbChannel->getManagers()) && !$this->authChecker->isGranted('ROLE_SUPER_ADMIN'))) {
            throw new \Exception('No.');
        }

        // --------------
        // ---- Form ----
        // --------------

        $form = $this->createFormBuilder($dbChannel)
            ->add('key', 'text', [
                'required' => false,
                'attr' => [
                    'placeholder' => 'Leave empty for no key.',
                    'help_text' => 'If a key is specified, channel components (commands, logs, etc) cannot be viewed without providing the key in the request URL.'
                ]
            ])
            ->add('command_prefix', 'text', [
                'required' => true,
                'attr' => [
                    'placeholder' => 'Maximum of one character. Default is \'.\'',
                    'help_text' => 'The command prefix for dynamic commands.'
                ]
            ])
            ->add('save', 'submit', ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // update document
            /** @var $manager \Doctrine\ODM\MongoDB\DocumentManager */
            $manager = $this->get('doctrine_mongodb')->getManager();
            $manager->persist($data);
            $manager->flush();

            // notify
            $request->getSession()->getFlashBag()->add('success', 'Channel successfully updated.');
        } else if ($form->isSubmitted() && !$form->isValid()) {
            $request->getSession()->getFlashBag()->add('error', 'There was an error updating the channel.');
        }

        // ----------------
        // ---- Render ----
        // ----------------

        return $this->render('KorobiWebBundle:controller/channel/admin:home.html.twig', [
            'network_name' => $dbNetwork->getName(),
            'channel_name' => $dbChannel->getChannel(),
            'form' => $form->createView()
        ]);
    }
}
