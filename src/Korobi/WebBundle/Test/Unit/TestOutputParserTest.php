<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Deployment\TestOutputParser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Korobi\WebBundle\Test\Unit
 */
class TestOutputParserTest extends WebTestCase {

    public function testParseLineWithFailures() {
        $data = (new TestOutputParser())->parseLine('Tests: 23, Assertions: 29, Failures: 1.');
        $this->assertEquals(23, $data['tests']);
        $this->assertEquals(29, $data['assertions']);
        $this->assertEquals(1, $data['failures']);
    }

    public function testParseLineWithIncomplete() {
        $data = (new TestOutputParser())->parseLine('Tests: 23, Assertions: 28, Incomplete: 1.');
        $this->assertEquals(23, $data['tests']);
        $this->assertEquals(22, $data['passed']);
        $this->assertEquals(28, $data['assertions']);
        $this->assertEquals(1, $data['incomplete']);
        $this->assertEquals("Tentative pass", $data['status']);
    }

    public function testParseLineWithPass() {
        $data = (new TestOutputParser())->parseLine('OK (24 tests, 35 assertions)');
        $this->assertEquals(24, $data['tests']);
        $this->assertEquals(35, $data['assertions']);
        $this->assertEquals('Pass', $data['status']);
    }

    public function testWhenNoExplicitPassNumber() {
        $data = (new TestOutputParser())->parseLine('OK (26 tests, 98 assertions)');
        $this->assertEquals(26, $data['tests']);
        $this->assertEquals(26, $data['passed']);
        $this->assertEquals(98, $data['assertions']);
        $this->assertEquals('Pass', $data['status']);
    }

    public function testWhenNoExplicitPassNumberWithFailures() {
        $data = (new TestOutputParser())->parseLine('Tests: 41, Assertions: 134, Failures: 2.');
        $this->assertEquals(41, $data['tests']);
        $this->assertEquals(39, $data['passed']);
        $this->assertEquals(134, $data['assertions']);
        $this->assertEquals('Fail', $data['status']);
    }

    public function testRealLifeExample() {
        $data = (new TestOutputParser())->parseLine('Tests: 25, Assertions: 61, Skipped: 4.');
        $this->assertEquals(
            '{"incomplete":"4","passed":21,"failures":0,"assertions":"61","status":"Tentative pass","tests":"25"}',
            json_encode($data)
        );
    }
}
