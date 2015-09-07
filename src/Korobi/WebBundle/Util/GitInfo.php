<?php

namespace Korobi\WebBundle\Util;

/**
 * Handle getting info about git branches, commits, etc.
 *
 * @package Korobi\WebBundle\Util
 */
class GitInfo {

    protected $branch;
    protected $hash;

    /**
     * @param $environment
     */
    public function __construct($environment) {
        $this->updateData($environment);
    }

    /**
     * Force update data.
     *
     * @param string $environment
     */
    public function updateData($environment = 'dev') {
        $branch = trim(`git rev-parse --abbrev-ref HEAD 2>&1`);
        if (StringUtil::startsWith($branch, "fatal: Not a git repository")) {
            $this->branch = $environment === 'prod' ? 'www1-stable' : '';
            $this->hash = "";
        } else {
            $this->branch = $branch;
            $this->hash = trim(`git rev-parse HEAD 2>&1`);
        }
    }

    /**
     * @return string The name of the current git branch.
     */
    public function getBranch() {
        return $this->branch;
    }

    /**
     * @return string The current hash.
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * @param int $length Length of short hash
     * @return string The shorter hash
     */
    public function getShortHash($length = 7) {
        return substr($this->hash, 0, $length);
    }
}
