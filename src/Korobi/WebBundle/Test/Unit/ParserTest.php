<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\Parser\LogParser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * Class ParserTest
 * @see LogParser
 * @package Korobi\WebBundle\Test\Unit
 */
class ParserTest extends WebTestCase {

    public function testShutUpPhpUnit() {
        $this->assertTrue(true);
    }

    public function testParseJoin() {
        /** @see LogParser::parseJoin */
        $chat = $this->getMockBuilder('Korobi\WebBundle\Document\Chat')
            ->disableOriginalConstructor()
            ->getMock();

        $chat->expects($this->any())->method("getDate")->will($this->returnValue(new \DateTime("@0")));
        $chat->expects($this->any())->method("getActorName")->will($this->returnValue("TestUser"));
        $chat->expects($this->any())->method("getActorPrefix")->will($this->returnValue("OPERATOR"));

        $this->assertEquals('[00:00:00] <span class="irc--14-99">** <span class="irc--09-99">@</span>TestUser joined the channel</span>', LogParser::parseJoin($chat));
    }



}
