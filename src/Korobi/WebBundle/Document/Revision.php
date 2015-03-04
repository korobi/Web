<?php

namespace Korobi\WebBundle\Document;

use Doctrine\ODM\MongoDB\Mapping\Annotations as MongoDB;

/**
 * @MongoDB\Document(collection="revisions",repositoryClass="Korobi\WebBundle\Repository\RevisionRepository")
 */
class Revision {

    /**
     * @MongoDB\Id(strategy="auto")
     */
    protected $id;

    /**
     * @MongoDB\String
     */
    private $old_commit;

    /**
     * @MongoDB\String
     */
    private $new_commit;

    /**
     * @MongoDB\Date
     */
    private $date;

    /**
     * @MongoDB\Boolean
     */
    private $deploy_successful;

    /**
     * @MongoDB\String
     */
    private $deploy_output;

    /**
     * @MongoDB\Boolean
     */
    private $tests_passed;

    /**
     * @MongoDB\String
     */
    private $tests_output;

    /**
     * @MongoDB\Boolean
     */
    private $manual;

    /**
     * @MongoDB\String
     */
    private $branch;

    /**
     * Get id
     *
     * @return id $id
     */
    public function getId() {
        return $this->id;
    }

    /**
     * Get oldCommit
     *
     * @return string $oldCommit
     */
    public function getOldCommit() {
        return $this->old_commit;
    }

    /**
     * Set oldCommit
     *
     * @param string $oldCommit
     * @return self
     */
    public function setOldCommit($oldCommit) {
        $this->old_commit = $oldCommit;
        return $this;
    }

    /**
     * Get newCommit
     *
     * @return string $newCommit
     */
    public function getNewCommit() {
        return $this->new_commit;
    }

    /**
     * Set newCommit
     *
     * @param string $newCommit
     * @return self
     */
    public function setNewCommit($newCommit) {
        $this->new_commit = $newCommit;
        return $this;
    }

    /**
     * Get date
     *
     * @return date $date
     */
    public function getDate() {
        return $this->date;
    }

    /**
     * Set date
     *
     * @param date $date
     * @return self
     */
    public function setDate($date) {
        $this->date = $date;
        return $this;
    }

    /**
     * Get deploySuccessful
     *
     * @return boolean $deploySuccessful
     */
    public function getDeploySuccessful() {
        return $this->deploy_successful;
    }

    /**
     * Set deploySuccessful
     *
     * @param boolean $deploySuccessful
     * @return self
     */
    public function setDeploySuccessful($deploySuccessful) {
        $this->deploy_successful = $deploySuccessful;
        return $this;
    }

    /**
     * Get deployOutput
     *
     * @return string $deployOutput
     */
    public function getDeployOutput() {
        return $this->deploy_output;
    }

    /**
     * Set deployOutput
     *
     * @param string $deployOutput
     * @return self
     */
    public function setDeployOutput($deployOutput) {
        $this->deploy_output = $deployOutput;
        return $this;
    }

    /**
     * Get testsPassed
     *
     * @return boolean $testsPassed
     */
    public function getTestsPassed() {
        return $this->tests_passed;
    }

    /**
     * Set testsPassed
     *
     * @param boolean $testsPassed
     * @return self
     */
    public function setTestsPassed($testsPassed) {
        $this->tests_passed = $testsPassed;
        return $this;
    }

    /**
     * Get testsOutput
     *
     * @return string $testsOutput
     */
    public function getTestsOutput() {
        return $this->tests_output;
    }

    /**
     * Set testsOutput
     *
     * @param string $testsOutput
     * @return self
     */
    public function setTestsOutput($testsOutput) {
        $this->tests_output = $testsOutput;
        return $this;
    }

    /**
     * Get whether deploy was done manually
     *
     * @return boolean $manual
     */
    public function getManual() {
        return $this->manual;
    }

    /**
     * Set whether deploy was done manually
     *
     * @param boolean $manual
     * @return self
     */
    public function setManual($manual) {
        $this->manual = $manual;
        return $this;
    }

    /**
     * Set branch
     *
     * @param string $branch
     * @return self
     */
    public function setBranch($branch)
    {
        $this->branch = $branch;
        return $this;
    }

    /**
     * Get branch
     *
     * @return string $branch
     */
    public function getBranch()
    {
        return $this->branch;
    }
}
