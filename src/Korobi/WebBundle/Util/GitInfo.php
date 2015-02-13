<?php


namespace Korobi\WebBundle\Util;

/**
 * Class to handle getting info about git branches, commits etc.
 * @package Korobi\WebBundle\Util
 */
class GitInfoUtility {

    protected $filesystem;
    protected $branch;
    protected $hash;

    /**
     * Initialize the class.
     */
    public function __construct() {
        $root = __DIR__ . "/../../../../"; // bit of a hack
        $ref = (new \SplFileObject($root . '.git/HEAD'))->getCurrentLine();
        $ref = trim(explode(' ', $ref)[1]);

        $this->branch = trim(array_reverse(explode('/', $ref))[0]);
        $this->hash = (new \SplFileObject($root . '.git/' . $ref))->getCurrentLine();
    }

    /**
     * @return string
     */
    public function getBranch() {
        return $this->branch;
    }

    /**
     * @return string
     */
    public function getHash() {
        return $this->hash;
    }

    /**
     * @param int $length Length
     * @return string
     */
    public function getShortHash($length = 8) {
        return substr($this->hash, 0, $length);
    }
}
