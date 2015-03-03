<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Parser\IRCColourParser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IRCColourParserTest extends WebTestCase {

    public function testSimpleColour() {
        $message = "\x0305Hello world!";
        $this->assertEquals(["foreground" => "05", "background" => "99", "skip" => 2], IRCColourParser::parseColour($message));
    }

    public function testSimpleColourWithSwap() {
        $message = "\x0305Hello world!";
        $this->assertEquals(["foreground" => "99", "background" => "05", "skip" => 2], IRCColourParser::parseColour($message, true));
    }

    public function testSimpleColourWithSwapAndDefaults() {
        $message = "\x0305Hello world!";
        $this->assertEquals(["foreground" => "42", "background" => "05", "skip" => 2], IRCColourParser::parseColour($message, true, 99, 42));
    }

    public function testSimpleColourWithBackground() {
        $message = "\x0305,04Hello world!";
        $this->assertEquals(["foreground" => "05", "background" => "04", "skip" => 5], IRCColourParser::parseColour($message));
    }

    public function testColoursWithSingleNumbers() {
        $message = "\x035,4Hello world!";
        $this->assertEquals(["foreground" => "05", "background" => "04", "skip" => 3], IRCColourParser::parseColour($message));
    }

    public function testColoursWithSwap() {
        $message = "\x035,4Hello world!";
        $this->assertEquals(["foreground" => "04", "background" => "05", "skip" => 3], IRCColourParser::parseColour($message, true));
    }

    public function testColoursWithDefaults() {
        $message = "\x035Hello world!";
        $this->assertEquals(["foreground" => "05", "background" => "02", "skip" => 1], IRCColourParser::parseColour($message, false, 99, 2));
    }

    public function testColoursWithDefaultsAndSwap() {
        // Normally this would be 05 colour text on a default (i.e. 99) BG
        // But we're swapping it and providing some defaults
        // So the background should be 05 and the foreground should be our default background colour
        $message = "\x035Hello world!";
        $this->assertEquals(["foreground" => "02", "background" => "05", "skip" => 1], IRCColourParser::parseColour($message, true, 99, 2));
    }

    public function testInvalidColourFragment() {
        $message = "Hello world!";
        $this->assertEquals(null, IRCColourParser::parseColour($message));
    }

}
