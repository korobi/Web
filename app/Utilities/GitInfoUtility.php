<?php


namespace Korobi\Utilities;


use Illuminate\Contracts\Filesystem\Filesystem;

class GitInfoUtility {

    protected $filesystem;

    public function __construct(Filesystem $fs) {
        $this->filesystem = $fs;
    }

    public function getShortHash($branch) {
        $root = base_path(".git/refs/heads/");
        return substr($this->filesystem->get($root . $branch), 0, 8);
    }

} 