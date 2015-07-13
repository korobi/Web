<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Util\AkioMessageBuilder;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Korobi\WebBundle\Test\Unit
 */
class AkioMessageBuilderTest extends WebTestCase {

    public function testSimpleMessage() {
        $akioMock = $this->getMockBuilder('\Korobi\WebBundle\Util\Akio')->disableOriginalConstructor()->getMock();
        $sut = new AkioMessageBuilder($akioMock);
        $sut = $sut->hotPink()->text('Hello!');
        $akioMock->expects($this->once())->method('sendMessage')->with($sut, 'type');
        $this->assertEquals($sut->getText(), '{C}13Hello!');
        $sut->send('type');
    }

    public function testMoreComplexMessage() {
        $akioMock = $this->getMockBuilder('\Korobi\WebBundle\Util\Akio')->disableOriginalConstructor()->getMock();
        $sut = new AkioMessageBuilder($akioMock);
        $sut = $sut->hotPink()->bold()->text('Hello ')->teal()->text('World!');
        $akioMock->expects($this->once())->method('sendMessage')->with($sut, 'type1');
        $this->assertEquals($sut->getText(), '{C}13{B}Hello {C}10World!');
        $sut->send('type1');
    }

}
