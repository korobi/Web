<?php

namespace Korobi\WebBundle\Util;

class FileCache {

    const KEY_VALIDATION = '/[a-z0-9\-_\.]/i';

    private $root;
    private $extension;

    public function __construct($root, $extension = '.cache') {
        if(!is_dir($root)) {
            mkdir($root, 0777, true);
        }
        $this->root = $root;
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
        if(is_array($key)) {
            $test = implode('', $key);
        }

        if(preg_match(self::KEY_VALIDATION, $test) === false) {
            throw new \InvalidArgumentException('Key must match the regex "' . self::KEY_VALIDATION . '"');
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
            if(is_dir($subpath)) {
                $this->removeRecursively($path . DIRECTORY_SEPARATOR . $subpath);
                rmdir($path);
            } else {
                unlink($subpath);
            }
        }
    }

}
