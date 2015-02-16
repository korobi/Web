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
        $this->assertTrue($crawler->filter('body.light')->count() === 0);
    }

    public function testBranchAndCommitDisplayedInFooter() {
        $client = static::createClient();

        $gitInfo = $this->getMockBuilder('Korobi\WebBundle\Util\GitInfo')
            ->disableOriginalConstructor()
            ->getMock();

        $gitInfo->expects($this->any())->method("getBranch")->will($this->returnValue("dummy_branch"));
        $gitInfo->expects($this->any())->method("getShortHash")->will($this->returnValue("1234567"));
        $client->getContainer()->set('korobi.git_info', $gitInfo);

        $crawler = $client->request('GET', '/');

        $this->assertTrue($crawler->filter('footer.footer .footer--copyright:contains("dummy_branch")')->count() > 0);
        $this->assertTrue($crawler->filter('footer.footer .footer--copyright:contains("1234567")')->count() > 0);
    }


}
