<?php

namespace Korobi\WebBundle\Deployment;

use Korobi\WebBundle\Document\Revision;
use Korobi\WebBundle\Document\User;
use Korobi\WebBundle\Util\AkioMessageBuilder;
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
     * @var AkioMessageBuilder[] Holds a queue of messages to send all at once.
     */
    protected $messageQueue = [];

    /**
     * @var string Environment name
     */
    protected $env;

    /**
     * @param $request Request
     * @param $revision Revision
     * @param $user User
     * @param $authorisationChecker AuthorizationChecker
     * @param $hmacKey string The HMAC key
     * @param $rootPath
     */
    public function __construct(Request $request, Revision $revision, $user, AuthorizationChecker $authorisationChecker, $hmacKey, $rootPath, $env) {
        $this->request = $request;
        $this->revision = $revision;
        $this->user = $user;
        $this->authorisationChecker = $authorisationChecker;
        $this->hmacKey = $hmacKey;
        $this->rootPath = $rootPath;
        $this->status = [];
        $this->env = $env;
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

    /**
     * @param AkioMessageBuilder $message The message to add.
     */
    public function addMessageToQueue(AkioMessageBuilder $message) {
        $this->messageQueue[] = $message;
    }

    /**
     * @return \Korobi\WebBundle\Util\AkioMessageBuilder[]
     */
    public function getAllMessagesInQueue() {
        return $this->messageQueue;
    }

    /**
     * @return string
     */
    public function getEnvironment() {
        return $this->env;
    }
}
