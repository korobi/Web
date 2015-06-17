<?php

namespace Korobi\WebBundle\Test\Unit;

use Korobi\WebBundle\Deployment\DeploymentLogger;
use Symfony\Bundle\FrameworkBundle\Test\WebTestCase;

/**
 * @package Korobi\WebBundle\Test\Unit
 * @see DeploymentLogger
 */
class DeploymentLoggerTest extends WebTestCase {

    public function testShutUpPhpUnit() {
        $this->assertTrue(true);
    }

    public function testDebugForError() {
        /** @see DeploymentLogger::debug */
        $loggerInterface = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $message = 'Hello World!';
        $context = [1, 2, 3, 4, 5, 6];
        $loggerInterface->expects($this->once())->method('error')->with($message, $context);

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();
        $kernel->expects($this->once())->method('isDebug')->will($this->returnValue(true));

        $sut = new DeploymentLogger($kernel, $loggerInterface);
        $sut->debug($message, $context, true);
    }

    public function testDebugForInfo() {
        /** @see DeploymentLogger::debug */
        $logger = $this->getMockBuilder('Psr\Log\LoggerInterface')
            ->disableOriginalConstructor()
            ->getMock();
        $message = 'Hello World!';
        $context = [1, 2, 3, 4, 5, 6];
        $logger->expects($this->once())->method('info')->with($message, $context);

        $kernel = $this->getMockBuilder('Symfony\Component\HttpKernel\Kernel')
            ->disableOriginalConstructor()
            ->getMock();
        $kernel->expects($this->once())->method('isDebug')->will($this->returnValue(true));

        $sut = new DeploymentLogger($kernel, $logger);
        $sut->debug($message, $context);
    }
}
