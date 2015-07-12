<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Util\ExcludedHomepageChannels;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Korobi\WebBundle\Test\Unit
 */
class ExcludedHomepageChannelsTest extends WebTestCase {

    public function testSimpleConfig() {
        // arrange
        $config = ['homepage_excluded_channels' => [['channel' => '#foo', 'network' => 'bar']]];

        // act
        $sut = new ExcludedHomepageChannels($config);

        // assert
        $this->assertTrue($sut->isBlacklisted("bar", "#foo"));
        $this->assertFalse($sut->isBlacklisted("foobar", "#foo"));
        $this->assertFalse($sut->isBlacklisted("foo", "#foobar"));
    }

}
