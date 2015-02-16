<?php

namespace Korobi\WebBundle\Test\Controller;

use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

class HomeControllerTest extends WebTestCase {

    public function testIndexLoadsSuccessfully() {
        $client = static::createClient();
        $client->request('GET', '/');

        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }

    public function testIndexWithLightTheme() {
        $client = static::createClient();

        $theme = $this->getMockBuilder('Korobi\WebBundle\Util\ThemeService')
            ->disableOriginalConstructor()
            ->getMock();

        $theme->expects($this->any())->method("isLight")->will($this->returnValue(true));
        $client->getContainer()->set('korobi.theme_service', $theme);

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('body.light')->count() > 0);
        // everything resets..
        $crawler = $client->request('GET', '/');
        $this->assertTrue($crawler->filter('body.light')->count() !== 0);
    }


}
