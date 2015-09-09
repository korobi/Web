<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Document\Chat;
use Korobi\WebBundle\IRC\Parser\IRCTextParser;
use Korobi\WebBundle\IRC\Parser\LogParser;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;
use Symfony\Component\Translation\TranslatorInterface;

class LogParserTest extends WebTestCase {

    public function testTransformActorEscaping() {
        $sut = new LogParser(new DummyTranslator());
        $result = $sut->transformActor("<strong>HTML</strong><marquee>yay</marquee>");
        $this->assertNotContains("<", $result, "Left angle bracket must not be in result.");
        $this->assertNotContains(">", $result, "Right angle bracket must not be in result.");
    }
}

class DummyTranslator implements TranslatorInterface {

    public function trans($id, array $parameters = array(), $domain = null, $locale = null) {
        return "Cat.";
    }

    public function transChoice($id, $number, array $parameters = array(), $domain = null, $locale = null) {
        return "Cat.";
    }

    public function setLocale($locale) {
        return;
    }

    public function getLocale() {
        return "Cat.";
    }
}
