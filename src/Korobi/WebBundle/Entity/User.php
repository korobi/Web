<?php

namespace Korobi\WebBundle\Entity;

use FOS\UserBundle\Model\User as BaseUser;
use Doctrine\ORM\Mapping as ORM;

/**
 * User
 */
class User extends BaseUser {
    /**
     * @var integer
     */
    protected $id;

    /**
     * @var string
     */
    protected $githubUserId;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return integer
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get githubUserId
     *
     * @return string
     */
    public function getGithubUserId() {
        return $this->githubUserId;
    }

    /**
     * Set githubUserId
     *
     * @param string $githubUserId
     * @return User
     */
    public function setGithubUserId($githubUserId) {
        $this->githubUserId = $githubUserId;

        return $this;
    }
}
