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
        $root = __DIR__ . '/../../../../'; // bit of a hack
        $ref = (new \SplFileObject($root . '.git/HEAD'))->getCurrentLine();
        $ref = trim(explode(' ', $ref)[1]);

        if($environment === 'prod' && file_exists($root . 'REVISION') && str_replace('refs/heads/', '', $ref) === 'deploy') {
            $this->branch = 'www1-stable';
            $this->hash = (new \SplFileObject($root . 'REVISION'))->getCurrentLine();
        } else {
            $this->branch = str_replace('refs/heads/', '', $ref);
            $this->hash = (new \SplFileObject($root . '.git/' . $ref))->getCurrentLine();
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
