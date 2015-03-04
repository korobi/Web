<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Deployment\TestOutputParser;
use Korobi\WebBundle\Parser\NickColours;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class TestOutputParserTest: so meta
 * @package Korobi\WebBundle\Test\Unit
 */
class TestOutputParserTest extends WebTestCase {

    public function testParseLineWithFailures() {
        $sut = new TestOutputParser();
        $data = $sut->parseLine("Tests: 23, Assertions: 29, Failures: 1.");
        $this->assertEquals(23, $data['tests']);
        $this->assertEquals(29, $data['assertions']);
        $this->assertEquals(1, $data['failures']);
    }

    public function testParseLineWithIncomplete() {
        $sut = new TestOutputParser();
        $data = $sut->parseLine("Tests: 23, Assertions: 28, Incomplete: 1.");
        $this->assertEquals(23, $data['tests']);
        $this->assertEquals(28, $data['assertions']);
        $this->assertEquals(1, $data['incomplete']);
        $this->assertEquals("Tentative pass", $data['status']);
    }

    public function testParseLineWithPass() {
        $sut = new TestOutputParser();
        $data = $sut->parseLine("OK (24 tests, 35 assertions)");
        $this->assertEquals(24, $data['tests']);
        $this->assertEquals(35, $data['assertions']);
        $this->assertEquals("Pass", $data['status']);
    }

}
