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
     * @param $rootDir
     */
    public function __construct($environment, $rootDir) {
        $this->updateData($rootDir, $environment);
    }

    /**
     * Force update data.
     *
     * @param string $environment
     */
    public function updateData($rootDir, $environment = 'dev') {
        if($environment === 'prod' || $environment === 'staging') {
            $this->branch = $environment == 'prod' ? 'www1-stable' : 'master';
            $this->hash = trim(file_get_contents($rootDir . '/../REVISION'));
            return;
        }

        $branch = trim(`git rev-parse --abbrev-ref HEAD 2>&1`);
        if (StringUtil::startsWith($branch, 'fatal: Not a git repository')) {
            $this->branch = 'unknown';
            $this->hash = 'unknown';
            return;
        }

        $this->branch = $branch;
        $this->hash = trim(`git rev-parse HEAD 2>&1`);
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
