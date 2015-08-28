<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Document\Channel;
use Korobi\WebBundle\Service\AuthenticationService;
use PHPUnit_Framework_TestCase;
use ReflectionClass;
use Symfony\Component\HttpFoundation\ParameterBag;
use Symfony\Component\Security\Core\Authorization\AuthorizationCheckerInterface;

class AuthenticationServiceTest extends PHPUnit_Framework_TestCase {

    public function testUnauthorised() {
        $sut = new AuthenticationService(new DummyAuthService(true));
        $stub = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();
        $reflection = new ReflectionClass($stub);
        $reflection_property = $reflection->getProperty('query');
        $reflection_property->setAccessible(true);
        $bag = new ParameterBag(["key" => "kitten"]);

        $reflection_property->setValue($stub, $bag);

        $channel = new Channel();
        $channel->setKey("cats");
        $this->assertFalse($sut->hasAccessToChannel($channel, $stub));
    }

    public function testAuthorisedByKey() {
        $sut = new AuthenticationService(new DummyAuthService(true));
        $stub = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();
        $reflection = new ReflectionClass($stub);
        $reflection_property = $reflection->getProperty('query');
        $reflection_property->setAccessible(true);
        $bag = new ParameterBag(["key" => "cats"]);

        $reflection_property->setValue($stub, $bag);

        $channel = new Channel();
        $channel->setKey("cats");
        $this->assertTrue($sut->hasAccessToChannel($channel, $stub));
    }

    public function testAuthorisedByAdmin() {
        $sut = new AuthenticationService(new DummyAuthService(false));
        $stub = $this->getMockBuilder('Symfony\Component\HttpFoundation\Request')
            ->getMock();
        $reflection = new ReflectionClass($stub);
        $reflection_property = $reflection->getProperty('query');
        $reflection_property->setAccessible(true);
        $bag = new ParameterBag([]);

        $reflection_property->setValue($stub, $bag);

        $channel = new Channel();
        $channel->setKey("cats");
        $this->assertTrue($sut->hasAccessToChannel($channel, $stub));
    }

}

class DummyAuthService implements AuthorizationCheckerInterface {

    private $reject;

    /**
     * DummyAuthService constructor.
     * @param $reject
     */
    public function __construct($reject) {
        $this->reject = $reject;
    }


    /**
     * Checks if the attributes are granted against the current authentication token and optionally supplied object.
     *
     * @param mixed $attributes
     * @param mixed $object
     *
     * @return bool
     */
    public function isGranted($attributes, $object = null) {
        return !$this->reject;
    }
}
