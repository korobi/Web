<?php

namespace Korobi\WebBundle\Security;

use FOS\UserBundle\Model\UserManagerInterface;
use HWI\Bundle\OAuthBundle\OAuth\Response\UserResponseInterface;
use HWI\Bundle\OAuthBundle\Security\Core\User\FOSUBUserProvider;
use Symfony\Component\Security\Core\User\UserInterface;

class UserProvider extends FOSUBUserProvider {

    private $secret;

    /**
     * @param UserManagerInterface $userManager
     * @param array $properties
     * @param $secret
     */
    public function __construct(UserManagerInterface $userManager, array $properties, $secret) {
        parent::__construct($userManager, $properties);
        $this->secret = $secret;
    }

    public function loadUserByOAuthUserResponse(UserResponseInterface $response) {
        $username = $response->getUsername();

        $user = $this->userManager->findUserBy([$this->getProperty($response) => $username]);

        // no existing user, create one from response data
        if ($user == null) {
            /** @var \Korobi\WebBundle\Document\User $user */
            $user = $this->userManager->createUser();
            $user->setGithubUserId($username);
            $user->setUsername($response->getNickname());
            $user->setEmail($response->getEmail() ?: $response->getNickname() . '@users.noreply.github.com');
            $user->setPlainPassword(hash('sha512', $username . $this->secret));
            $user->setEnabled(true);

            $this->userManager->updateUser($user);

            return $user;
        }

        /** @var \Korobi\WebBundle\Document\User $user */
        $user = parent::loadUserByOAuthUserResponse($response);

        return $user;
    }

    public function connect(UserInterface $user, UserResponseInterface $response) {
        parent::connect($user, $response);
    }
}
