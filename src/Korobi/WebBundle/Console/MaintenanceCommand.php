<?php

namespace Korobi\WebBundle\Console;

use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class MaintenanceCommand extends ContainerAwareCommand {

    const MAINTENANCE_FILE = __DIR__ . '/../../maintenance';

    protected function configure() {
        $this
            ->setName('korobi:maintenance')
            ->setDescription('Toggle maintenance mode')
            ->addArgument('option', InputArgument::OPTIONAL, 'Enable or disable maintenance mode.');
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        $option = $in->getArgument('option');

        if (empty($option)) {
            $out->writeln('Maintenance mode is currently ' . ($this->inMaintenance() ? 'enabled' : 'disabled'));
            return;
        }

        $option = strtolower($option);
        if ($option === 'off' || $option === 'false') {
            if (!$this->inMaintenance()) {
                $out->writeln('Maintenance mode is already disabled.');
            } else {
                $mt = $this->maintenance(false);
                $out->writeln('Maintenance mode: ' . ($mt ? 'Disabled' : 'Could not unlink file.'));
            }
        } else if ($option === 'on' || $option === 'true') {
            if ($this->inMaintenance()) {
                $out->writeln('Already in maintenance mode.');
            } else {
                $mt = $this->maintenance(true);
                $out->writeln('Maintenance mode: ' . ($mt ? 'Enabled' : 'Could not create file.'));
            }
        } else {
            $out->writeln('Invalid Usage. Option needs to be either "on" or "off".');
        }
    }

    /**
     * @return bool
     */
    protected function inMaintenance() {
        return file_exists(MaintenanceCommand::MAINTENANCE_FILE);
    }

    /**
     * @param bool $option
     *
     * @return bool
     */
    protected function maintenance($option) {
        if ($option) {
            return touch(MaintenanceCommand::MAINTENANCE_FILE);
        } else {
            return unlink(MaintenanceCommand::MAINTENANCE_FILE);
        }
    }
}
