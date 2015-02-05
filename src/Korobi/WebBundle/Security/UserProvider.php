<?php

namespace Korobi\WebBundle\Security;

use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider extends FOSUBUserProvider {
    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        $username = $response->getUsername();

        $user = $this->userManager->findUserBy([$this->getProperty($response) => $username]);

        if ($user == null) {
            /** @var $user \Korobi\WebBundle\Entity\User */
            $user = $this->userManager->createUser();
            $user->setGithubUserId($username);
            $user->setUsername($response->getNickname());
            $user->setEmail($response->getEmail());
            $user->setPlainPassword($username . '__meow');
            $user->setEnabled(true);

            $this->userManager->updateUser($user);

            return $user;

        }

        /** @var $user \Korobi\WebBundle\Entity\User */
        $user = parent::loadUserByOAuthUserResponse($response);
        $user->setGithubUserId($username);

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response) {
        parent::connect($user, $response);
    }
}
