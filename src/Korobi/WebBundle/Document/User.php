<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;
use FOS\UserBundle\Model\User as BaseUser;

/**
 * @MongoDB\Document(collection="users",repositoryClass="Korobi\WebBundle\Repository\UserRepository")
 */
class User extends BaseUser {

    /**
     * @MongoDB\Id
     */
    protected $id;

    /**
     * @MongoDB\Field(type="int")
     */
    protected $githubUserId;

    public function __construct() {
        parent::__construct();
    }

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get githubUserId
     *
     * @return int $githubUserId
     */
    public function getGithubUserId() {
        return $this->githubUserId;
    }

    /**
     * Set githubUserId
     *
     * @param int $githubUserId
     * @return self
     */
    public function setGithubUserId($githubUserId) {
        $this->githubUserId = $githubUserId;
        return $this;
    }
}
