<?php

namespace Korobi\WebBundle\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class MongoProvisionCommand extends ContainerAwareCommand {
    const DEFAULT_CHANNEL = "#korobi";
    const DEFAULT_NETWORK = "esper";

    protected function configure() {
        $this
            ->setName('korobi:db:provision')
            ->setDescription('Add new entries to the database')
            ->addArgument('channel', InputArgument::OPTIONAL, 'Channel to provision. Include the #', MongoProvisionCommand::DEFAULT_CHANNEL)
            ->addArgument('network', InputArgument::OPTIONAL, 'Network to provision on', MongoProvisionCommand::DEFAULT_NETWORK)
            ->addOption('dry', 'd', InputOption::VALUE_NONE, 'Do not commit to the database.');
    }

    protected function execute(InputInterface $in, OutputInterface $out)
    {
        $channel = $in->getArgument('channel');
        $network = $in->getArgument('network');
        $dry_run = $in->getOption('dry');

        if (!$this->validShellArgument($channel)) {
            $out->writeln("Aborting. Your channel argument ($channel) contains unsafe characters.");
            return;
        }

        if (!$this->validShellArgument($network)) {
            $out->writeln("Aborting. Your network argument ($network) contains unsafe characters.");
            return;
        }

        $provisionScript = __DIR__ . "/../../../../templates/mongo_provision.js";
        $mongoArgs = "--eval='var channel_name = \"$channel\"; var network_name = \"$network\"'";

        $out->writeln("Provisioning $channel on $network");

        if ($dry_run) {
            $out->writeln("Dry run: mongo $mongoArgs korobi $provisionScript");
        } else {
            exec("mongo $mongoArgs korobi $provisionScript", $output, $exit_code);
            $out->writeln($output);
            $out->writeln("Operation exited with exit code: $exit_code");
        }
    }

    protected function validShellArgument($arg) {
        return preg_match('/[\'\"\`]/', $arg) === 0;
    }
}
