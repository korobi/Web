<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Util\FileCache;
use Korobi\WebBundle\Util\FileUtil;
use Symfony\Bundle\FrameworkBundle\Test\KernelTestCase;

class FileCacheTest extends KernelTestCase {

    /** @var FileCache */
    private $cache;
    private $cacheFolder;

    public function setup() {
        static::bootKernel();
        $this->cacheFolder = static::$kernel->getCacheDir() . DIRECTORY_SEPARATOR . 'file-cache-test';
        $this->cache = new FileCache($this->cacheFolder, '.cache');
    }

    public function teardown() {
        $this->cache = null;
        FileUtil::removeRecursively($this->cacheFolder);
    }

    private function path($key) {
        return $this->cacheFolder . DIRECTORY_SEPARATOR . $key;
    }

    public function testExists() {
        touch($this->path('a.cache'));
        $this->assertTrue($this->cache->exists('a'));
    }

    public function testDoesntExists() {
        $this->assertFalse($this->cache->exists('a'));
    }

    public function testGetSet() {
        $this->cache->set('a', ['item']);

        $value = $this->cache->get('a');
        $this->assertTrue(is_array($value));
        $this->assertEquals('item', $value[0]);
    }

    public function testRemove() {
        $this->cache->set('a', 1);
        $this->assertTrue(is_file($this->path('a.cache')));

        $this->cache->remove('a');
        $this->assertFalse(is_file($this->path('a.cache')));
    }

    public function testNesting() {
        $this->cache->set(['a', 'b', 'c'], 1);

        $this->assertTrue(is_file($this->path('a/b/c.cache')));
    }

}
