<?php


namespace Korobi\WebBundle\Command;


use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsGenerationCommand extends ContainerAwareCommand {
    protected function configure() {
        $this
            ->setName('korobi:statgen')
            ->setDescription('Generate stats [for a channel]')
            ->addArgument(
                'channel',
                InputArgument::OPTIONAL,
                'The channel name to generate stats for'
            );
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        $name = $input->getArgument('channel');

        $output->writeln("You specified $name");
    }
}
