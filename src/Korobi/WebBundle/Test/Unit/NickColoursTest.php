<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Parser\NickColours;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class NickColoursTest extends WebTestCase {

    public function testSomeNicks() {
        $this->assertSame('99', NickColours::getColourForNick("mbaxter"), "mbaxter's nick is default");
        $this->assertSame('07', NickColours::getColourForNick("Zarthus"), "Zarthus's nick is a sandy brown colour");
        $this->assertSame('12', NickColours::getColourForNick("Kashike"), "Kashike's nick is a light blue");
    }

}
