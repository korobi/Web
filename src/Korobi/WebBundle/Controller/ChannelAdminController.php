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
        /** @var Network $dbNetwork */
        /** @var Channel $dbChannel */
        list($dbNetwork, $dbChannel) = $this->createNetworkChannelPair($network, $channel);

        // check if authorized
        /** @var User $user */
        $user = $this->getUser();
        /** @noinspection PhpParamsInspection */
        if ($user === null || (!in_array($user->getGitHubUserId(), $dbChannel->getManagers()) && !$this->authChecker->isGranted('ROLE_SUPER_ADMIN'))) {
            throw $this->createAccessDeniedException("You must be a channel manager to access this page");
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
            ->add('commands_enabled', 'checkbox', [
                'required' => false,
                'attr' => [
                    'help_text' => 'Should dynamic commands be enabled for this channel?'
                ]
            ])
            ->add('punishments_enabled', 'checkbox', [
                'required' => false,
                'attr' => [
                    'help_text' => 'Should punishments be enabled for this channel?'
                ]
            ])
            ->add('permissions', 'collection', [
                'required' => false,
                'type' => 'text',
                'allow_add' => true,
                'allow_delete' => true,
                'delete_empty' => true,
                'prototype' => true,
                'by_reference' => false,
                'attr' => [
                    'help_text' => ''
                ],
                'options' => [
                    'label' => false
                ],
            ])
            ->add('save', 'submit', ['label' => 'Save'])
            ->getForm();

        $form->handleRequest($request);

        if ($form->isValid()) {
            $data = $form->getData();

            // update document
            /** @var \Doctrine\ODM\MongoDB\DocumentManager $manager */
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
