<?php

namespace Korobi\WebBundle\Test\Unit;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class IRCColourTest extends WebTestCase {

    public function testSimpleColour() {
        $this->assertEquals("\x0305", "\x0305");
    }
}