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
        $chat->expects($this->any())->method("getActorHostname")->will($this->returnValue("user@host"));

        $this->assertEquals('[00:00:00] <span class="irc--14-99">** <span class="irc--09-99">@</span>TestUser (user@host) joined the channel</span>', LogParser::parseJoin($chat));
    }

    public function testParseMessage() {
        /** @see LogParser::parseMessage */
        $chat = $this->getMockBuilder('Korobi\WebBundle\Document\Chat')
            ->disableOriginalConstructor()
            ->getMock();

        $chat->expects($this->any())->method("getDate")->will($this->returnValue(new \DateTime("@0")));
        $chat->expects($this->any())->method("getActorName")->will($this->returnValue("TestUser"));
        $chat->expects($this->any())->method("getActorPrefix")->will($this->returnValue("OPERATOR"));
        $chat->expects($this->any())->method("getMessage")->will($this->returnValue("\x0307,04Hello!"));
        $this->assertEquals('[00:00:00] &lt;<span class="irc--09-99">@</span><span class="irc--06-99">TestUser</span>&gt; <span class="irc--07-04">Hello!</span>', LogParser::parseMessage($chat));
    }


    public function testParseMessageWithReverseColour() {
        /** @see LogParser::parseMessage */
        $chat = $this->getMockBuilder('Korobi\WebBundle\Document\Chat')
            ->disableOriginalConstructor()
            ->getMock();

        $chat->expects($this->any())->method("getDate")->will($this->returnValue(new \DateTime("@0")));
        $chat->expects($this->any())->method("getActorName")->will($this->returnValue("TestUser"));
        $chat->expects($this->any())->method("getActorPrefix")->will($this->returnValue("OPERATOR"));
        $chat->expects($this->any())->method("getMessage")->will($this->returnValue("\x0307,04\x16Hello!"));
        $this->assertEquals('[00:00:00] &lt;<span class="irc--09-99">@</span><span class="irc--06-99">TestUser</span>&gt; <span class="irc--07-04"><span class="irc--04-07">Hello!</span></span>', LogParser::parseMessage($chat));
    }



}
