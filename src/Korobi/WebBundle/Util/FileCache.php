<?php

namespace Korobi\WebBundle\Util;

class FileCache {

    private $root;
    private $extension;

    public function __construct($root, $extension = '.cache') {
        if(!is_dir($root)) {
            mkdir($root, 0777, true);
        }
        $this->root = rtrim($root, '/\\') . DIRECTORY_SEPARATOR;
        $this->extension = $extension;
    }

    public function exists($key) {
        $path = $this->getPath($this->checkKey($key));
        return is_file($path);
    }

    public function get($key) {
        if($this->exists($key)) {
            return unserialize(file_get_contents($this->getPath($key)));
        }
        return null;
    }

    public function set($key, $value) {
        $this->checkKey($key);

        $dir = $this->getDir($key);
        if(!is_dir($dir)) {
            mkdir($dir, 0777, true);
        }
        file_put_contents($this->getPath($key), serialize($value));
        return $this;
    }

    public function remove($key) {
        $path = $this->getPath($this->checkKey($key));
        if(file_exists($path)) {
            if(is_dir($path)) {
                $this->removeRecursively($path);
            } else {
                unlink($path);
            }
        }
    }

    private function checkKey($key) {
        $test = is_array($key) ? implode('', $key) : $key;

        if(strpos($test, DIRECTORY_SEPARATOR) !== false) {
            throw new \InvalidArgumentException('Key must not contain directory separator');
        }

        return $key;
    }

    private function getDir($key) {
        if(is_array($key)) {
            $key = implode(DIRECTORY_SEPARATOR, array_slice($key, 0, -1));
        }
        return $this->root . $key;
    }

    private function getPath($key) {
        if(is_array($key)) {
            $key = implode(DIRECTORY_SEPARATOR, $key);
        }
        return $this->root . $key . $this->extension;
    }

    private function removeRecursively($path) {
        foreach(array_diff(scandir($path), ['.', '..']) as $subpath) {
            $subpath = $path . DIRECTORY_SEPARATOR . $subpath;
            if(is_dir($subpath)) {
                $this->removeRecursively($subpath);
            }
        }
        foreach(array_diff(scandir($path), ['.', '..']) as $subpath) {
            unlink($path . DIRECTORY_SEPARATOR . $subpath);
        }
        rmdir($path);
    }

}
