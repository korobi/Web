<?php

namespace Korobi\WebBundle\Test\Controller;

use Korobi\WebBundle\Document\Network;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Korobi\WebBundle\Test\Controller
 * @see ChannelController
 */
class ChannelControllerTest extends WebTestCase {

    public function testXssIsNotPossible() {
        $client = static::createClient();
        // This is really not very testable because we don't use DI/follow SOLID properly in the ChannelController class.
        $netRepo = $this->getMockBuilder('Korobi\WebBundle\Repository\NetworkRepository')
            ->disableOriginalConstructor()
            ->getMock();
        $fakeNetwork = new Network();
        $fakeNetwork->setName("esper");
        $fakeNetwork->setSlug("esper");
        $netRepo->expects($this->once())
            ->method('findNetwork')
            ->with("esper")
            ->will($this->returnValue($fakeNetwork));

        // Last, mock the EntityManager to return the mock of the repository
        $entityManager = $this->getMockBuilder('\Doctrine\Common\Persistence\ObjectManager')
            ->disableOriginalConstructor()
            ->getMock();
        $entityManager->expects($this->once())
            ->method('getRepository')
            ->will($this->returnValue($netRepo));
        $client->request('GET', '/esper/korobi/logs/2015-03-14');
        $this->markTestSkipped("NYI");
        $this->assertEquals(200, $client->getResponse()->getStatusCode());
    }
}
