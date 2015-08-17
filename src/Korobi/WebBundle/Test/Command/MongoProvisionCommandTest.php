<?php

use Korobi\WebBundle\Console\MongoProvisionCommand;
use Symfony\Component\Console\Application;
use Symfony\Component\Console\Tester\CommandTester;

class MongoProvisionCommandTest extends \PHPUnit_Framework_TestCase {

    public function testExecute() {
        $application = new Application();
        $application->add(new MongoProvisionCommand());

        $command = $application->find('korobi:db:provision');
        $commandTester = new CommandTester($command);

        $commandTester->execute(['command' => $command->getName(), '--dry' => true]);
        $this->assertRegExp('/.*Dry run/', $commandTester->getDisplay());

        $commandTester->execute(['command' => $command->getName(), 'channel' => '#korobi', 'network' => 'esper', '--dry' => true]);
        $this->assertRegExp('/.*Dry run/', $commandTester->getDisplay());

        $commandTester->execute(['command' => $command->getName(), 'channel' => '#kor"obi', 'network' => 'esper', '--dry' => true]);
        $this->assertRegExp('/^Aborting\. Your channel/', $commandTester->getDisplay());

        $commandTester->execute(['command' => $command->getName(), 'channel' => '#korobi', 'network' => 'es"per', '--dry' => true]);
        $this->assertRegExp('/^Aborting\. Your network/', $commandTester->getDisplay());
    }
}
