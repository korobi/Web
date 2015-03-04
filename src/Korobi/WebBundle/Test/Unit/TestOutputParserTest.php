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

    public function testParseLine() {
        $sut = new TestOutputParser();
        $data = $sut->parseLine("Tests: 23, Assertions: 29, Failures: 1.");
        $this->assertEquals(23, $data['tests']);
        $this->assertEquals(29, $data['assertions']);
        $this->assertEquals(1, $data['failures']);
    }

}
