<?php

namespace Korobi\Util;

use Illuminate\Filesystem\Filesystem;

class GitInfoUtility {

    protected $filesystem;
    protected $branch;
    protected $hash;

    public function __construct(Filesystem $fs) {
        $this->filesystem = $fs;
        $ref = trim(explode(' ', $this->filesystem->get(base_path('.git/HEAD')))[1]);
        $this->branch = trim(array_reverse(explode('/', $ref))[0]);
        $root = base_path('.git/' . $ref);
        $this->hash = $this->filesystem->get($root);
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
