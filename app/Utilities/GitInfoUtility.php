<?php


namespace Yukai\Utilities;


use Illuminate\Contracts\Filesystem\Filesystem;

class GitInfoUtility {

    public function getShortHash(Filesystem $fs, $branch) {
        $root = base_path(".git/refs/heads/");
        return substr($fs->get($root . $branch), 0, 8);
    }

} 