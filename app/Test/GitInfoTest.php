<?php

namespace Korobi\Test;

use Korobi\Util\GitInfoUtility;
use Mockery;

class GitInfoTest extends TestCase {

    public function testGetHash() {
        $fs = $this->mock('\Illuminate\Filesystem\Filesystem');
        $fs->shouldReceive("get")->withArgs([base_path(".git/refs/heads/branch")])->andReturn("12345678901234567890");
        $fs->shouldReceive("get")->withArgs([base_path(".git/HEAD")])->andReturn("ref: refs/heads/branch");
        $sut = new GitInfoUtility($fs);
        $this->assertEquals("12345678", $sut->getShortHash());
        $this->assertEquals("branch", $sut->getBranch());
    }

    public function testGetHashIntegration() {
        $sut = new GitInfoUtility($this->app->make('\Illuminate\Filesystem\Filesystem'));
        echo "Does " . $sut->getShortHash() . " on branch " . $sut->getBranch() . " look correct?\n";
    }
}
