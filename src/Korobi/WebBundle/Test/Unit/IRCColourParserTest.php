<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Parser\IRCColourParser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IRCColourParserTest extends WebTestCase {

    public function testSimpleColour() {
        $message = "\x0305Hello world!";
        $this->assertEquals(["foreground" => "05", "background" => "99"], IRCColourParser::parseColour($message));
    }

    public function testSimpleColourWithBackground() {
        $message = "\x0305,04Hello world!";
        $this->assertEquals(["foreground" => "05", "background" => "04"], IRCColourParser::parseColour($message));
    }

    public function testInvalidColourFragment() {
        $message = "Hello world!";
        $this->assertEquals(null, IRCColourParser::parseColour($message));
    }

}
