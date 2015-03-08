<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Parser\IRCTextParser;
use Korobi\WebBundle\Parser\NickColours;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NickColoursTest extends WebTestCase {

    public function testSomeNicks() {
        $this->assertSame(IRCTextParser::DEFAULT_FOREGROUND, NickColours::getColourForNick("mbaxter"), "mbaxter's nick is default");
        $this->assertSame(7, NickColours::getColourForNick("Zarthus"), "Zarthus's nick is a sandy brown colour");
        $this->assertSame(12, NickColours::getColourForNick("Kashike"), "Kashike's nick is a light blue");
    }

}
