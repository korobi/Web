<?php

namespace Korobi\WebBundle\Util;

class FileCache {

    private $root;
    private $extension;
    private $stats;
    private $statPath;

    public function __construct($root, $extension = '.cache') {
        if($extension == '.cache-stat') {
            throw new \InvalidArgumentException('Extension cannot be .cache-stat');
        }
        if(!is_dir($root)) {
            mkdir($root, 0777, true);
        }
        $this->root = rtrim($root, '/\\') . DIRECTORY_SEPARATOR;
        $this->extension = $extension;

        $this->statPath = $this->root . 'stats.cache-stat';
        if(is_file($this->statPath)) {
            $this->stats = unserialize(file_get_contents($this->statPath));
        } else {
            $this->stats = [
                'hits' => 0,
                'misses' => 0,
                'changes' => 0,
            ];
        }
    }

    public function __destruct() {
        file_put_contents($this->statPath, serialize($this->stats));
    }

    public function getStats() {
        return $this->stats;
    }

    public function exists($key) {
        $path = $this->getPath($this->checkKey($key));
        $exists = is_file($path);

        ++$this->stats[$exists ? 'hits' : 'misses'];

        return $exists;
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

        ++$this->stats['changes'];

        return $this;
    }

    public function remove($key) {
        $path = $this->getPath($this->checkKey($key), false);

        if(is_dir($path)) {
            return $this->removeRecursively($path);
        } else if(is_file($path . $this->extension)) {
            unlink($path . $this->extension);
            return 1;
        }

        return 0;
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

    private function getPath($key, $includeExtension = true) {
        if(is_array($key)) {
            $key = implode(DIRECTORY_SEPARATOR, $key);
        }
        if($includeExtension) {
            $key .= $this->extension;
        }
        return $this->root . $key;
    }

    private function removeRecursively($path) {
        $count = 0;

        foreach(array_diff(scandir($path), ['.', '..']) as $subpath) {
            $subpath = $path . DIRECTORY_SEPARATOR . $subpath;
            if(is_dir($subpath)) {
                $count += $this->removeRecursively($subpath);
            }
        }

        foreach(array_diff(scandir($path), ['.', '..']) as $subpath) {
            unlink($path . DIRECTORY_SEPARATOR . $subpath);
            ++$count;
        }

        rmdir($path);
        return $count + 1;
    }

}
