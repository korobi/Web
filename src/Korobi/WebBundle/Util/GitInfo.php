<?php

namespace Korobi\WebBundle\Util;

/**
 * Handle getting info about git branches, commits, etc.
 *
 * @package Korobi\WebBundle\Util
 */
class GitInfo {

    const ONLINE_ENVS = ['staging', 'prod'];

    protected $branch;
    protected $hash;
    protected $tempRef;

    /**
     * @param string $environment Current environment's name.
     * @param string $rootDir Absolute path to root directory.
     */
    public function __construct($environment, $rootDir) {
        $this->updateData($rootDir, $environment);
    }

    /**
     * Force update data.
     *
     * @param string $rootDir Absolute path to root directory.
     * @param string $environment Current environment's name.
     */
    public function updateData($rootDir, $environment) {
        // This process is not simple! Please be careful when maintaining this code.
        // Please refer to the flowchart in the PR for further information on how this works.
        // https://github.com/korobi/Web/pull/211

        // Initialize the stack of functions to use
        $processStack = [];
        // The first step is to ensure we have a valid .git folder
        $processStack[] = 'checkIsGitRepo';
        // Next, we'll make sure there's a .git/HEAD file and process it
        $processStack[] = 'checkForGitHeadFile';
        // These are now fallbacks, for if the .git/HEAD approach failed
        // Here, we look for the packed-refs file and parse it
        $processStack[] = 'checkForPackedRefs';
        // Finally, if all else fails we use bendem's shell command approach
        // this is a bit slower but hopefully is more reliable.
        $processStack[] = 'useFallbackCommand';

        $gitDir = $rootDir . DIRECTORY_SEPARATOR . '.git' . DIRECTORY_SEPARATOR;
        foreach ($processStack as $item) {
            if ($this->$item($gitDir)) {
                break;
            }
        }

        // Handle capifony
        $this->postCheckForCapifonyFiles($gitDir, $environment);
    }

    /*
     * Begin chain-of-responsibility function definitions
     * --------------------------------------------------
     * These return true if the data has been set or false
     * to suggest using the next method to find the
     * requested information.
     *
     * Basically, see the return value as an "I'm done" or
     * "I'm not done yet".
     */

    private function checkIsGitRepo($gitDir) {
        if (file_exists($gitDir)) {
            return false;
        } else {
            $this->setBranchAndHash('unknown', 'unknown');
            return true;
        }
    }

    /** ------------------------------------------------------------ */

    private function checkForGitHeadFile($gitDir) {
        if (file_exists($gitDir . 'HEAD')) {
            $headContents = fgets(fopen($gitDir . 'HEAD', 'r')); // resource acquisition is initialization
            return $this->handleGitHeadFile($gitDir, $headContents);
        } else {
            $this->setBranchAndHash('unknown', 'unknown');
            return true;
        }
    }

    private function handleGitHeadFile($gitDir, $headContents) {
        if (strpos($headContents, 'ref: ') === false) {
            $this->setBranchAndHash($headContents, $headContents);
            return true;
        } else {
            $referencedFile = trim(substr($headContents, 5));
            $this->branch = str_replace('refs/heads/', '', $referencedFile);
            $this->tempRef = $referencedFile;
            if (!file_exists($gitDir . $referencedFile)) {
                return false;
            } else {
                $this->hash = file_get_contents($gitDir . $referencedFile);
                return true;
            }
        }
    }

    /** ------------------------------------------------------------ */

    private function checkForPackedRefs($gitDir) {
        $refToLocate = $this->tempRef;
        $packedRefFileLocation = $gitDir . 'packed-refs';
        if (file_exists($packedRefFileLocation)) {
            $packedRefData = file($packedRefFileLocation);
            foreach ($packedRefData as $line) {
                if (strpos($line, $refToLocate) !== false) {
                    $this->hash = explode(' ', $line)[0];
                    return true;
                }
            }

        }
        return false;
    }

    /** ------------------------------------------------------------ */

    private function useFallbackCommand($gitDir) {
        chdir($gitDir);
        $this->branch = trim(`git rev-parse --abbrev-ref HEAD 2>&1`);
        $this->hash = trim(`git rev-parse HEAD 2>&1`);
        return true;
    }

    /*
     * --------------------------------------------------
     * End chain-of-responsibility function definitions
     */

    private function postCheckForCapifonyFiles($gitDir, $environment) {
        $revisionFilePath = $gitDir . '..' . DIRECTORY_SEPARATOR . 'REVISION';
        if (file_exists($revisionFilePath)) {
            $this->hash = file_get_contents($revisionFilePath);
            if ($environment === 'prod' && $this->branch === 'deploy') {
                $this->branch = 'www1-stable';
            }
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

    private function setBranchAndHash($branch, $hash) {
        $this->branch = $branch;
        $this->hash = $hash;
    }
}
