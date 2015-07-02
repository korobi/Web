<?php

namespace Korobi\WebBundle\Deployment;

use Korobi\WebBundle\Document\Revision;
use Korobi\WebBundle\Document\User;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\Security\Core\Authorization\AuthorizationChecker;

/**
 * Holds fields relevant to the deployment request.
 * @package Korobi\WebBundle\Deployment
 */
class DeploymentInfo {

    /**
     * @var Request
     */
    private $request;

    /**
     * @var Revision
     */
    private $revision;

    /**
     * @var User|null
     */
    private $user;

    /**
     * @var AuthorizationChecker
     */
    private $authorisationChecker;

    /**
     * @var string
     */
    private $hmacKey;

    /**
     * @var string
     */
    private $rootPath;

    /**
     * @var array Array of statuses
     */
    private $status;

    /**
     * @param $request Request
     * @param $revision Revision
     * @param $user User
     * @param $authorisationChecker AuthorizationChecker
     * @param $hmacKey string The HMAC key
     * @param $rootPath
     */
    public function __construct(Request $request, Revision $revision, $user, AuthorizationChecker $authorisationChecker, $hmacKey, $rootPath) {
        $this->request = $request;
        $this->revision = $revision;
        $this->user = $user;
        $this->authorisationChecker = $authorisationChecker;
        $this->hmacKey = $hmacKey;
        $this->rootPath = $rootPath;
        $this->status = [];
    }

    /**
     * @return Request
     */
    public function getRequest() {
        return $this->request;
    }

    /**
     * @return Revision
     */
    public function getRevision() {
        return $this->revision;
    }

    /**
     * @return User
     */
    public function getUser() {
        return $this->user;
    }

    /**
     * @return AuthorizationChecker
     */
    public function getAuthorisationChecker() {
        return $this->authorisationChecker;
    }

    /**
     * @return string
     */
    public function getHmacKey() {
        return $this->hmacKey;
    }

    /**
     * @return string
     */
    public function getRootPath() {
        return $this->rootPath;
    }

    /**
     * @param string $status
     * @see  DeploymentStatus
     */
    public function addStatus($status) {
        $this->status[] = $status;
        $this->revision->setStatuses($this->status);
    }
}
