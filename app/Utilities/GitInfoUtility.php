<?php


namespace Korobi\Utilities;


use Illuminate\Contracts\Filesystem\Filesystem;

class GitInfoUtility {

    protected $filesystem;

    public function __construct(Filesystem $fs) {
    }

    public function getShortHash($branch) {
        $root = base_path(".git/refs/heads/");
        return substr($fs->get($root . $branch), 0, 8);
    }

} 