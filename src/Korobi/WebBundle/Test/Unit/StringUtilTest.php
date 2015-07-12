<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Util\StringUtil;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Korobi\WebBundle\Test\Unit
 * @see Korobi\WebBundle\Util\StringUtil
 */
class StringUtilTest extends WebTestCase {

    public function testStartsWith() {
        $this->assertTrue(StringUtil::startsWith('abc', 'a'));
        $this->assertTrue(StringUtil::startsWith('The quick brown fox', 'T'));
        $this->assertTrue(StringUtil::startsWith('The quick brown fox', 'The quick'));
        $this->assertTrue(StringUtil::startsWith('The quick brown fox', 'the quick', true));
        $this->assertFalse(StringUtil::startsWith('The quick brown fox', 'the quick'));
        $this->assertFalse(StringUtil::startsWith('abcdef', 'xtg'));
    }

    public function testEndsWith() {
        $this->assertTrue(StringUtil::endsWith('abc', 'c'));
        $this->assertTrue(StringUtil::endsWith('The quick brown fox', 'fox'));
        $this->assertTrue(StringUtil::endsWith('The quick brown fox', 'brown fox'));
        $this->assertTrue(StringUtil::endsWith('The quick brown fox', 'quick brOWn Fox', true));
        $this->assertFalse(StringUtil::endsWith('The quick brown fox', 'BROWN FOX'));
        $this->assertFalse(StringUtil::endsWith('abcdef', 'xtg'));
    }

    public function testStringContains() {
        $this->assertTrue(StringUtil::stringContains('abc', 'c'));
        $this->assertFalse(StringUtil::stringContains('zed', 'c'));
    }
}
