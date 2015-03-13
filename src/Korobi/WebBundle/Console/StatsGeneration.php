<?php


namespace Korobi\WebBundle\Console;


use Korobi\WebBundle\Util\InfluxService;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console\Output\OutputInterface;

class StatsGeneration extends Command {
    /**
     * @var InfluxService
     */
    private $influx;

    /**
     * @param InfluxService $influx
     */
    public function __construct(InfluxService $influx) {

        parent::__construct();
        $this->influx = $influx;
    }

    protected function configure() {

        $this
            ->setName('korobi:statsgen')
            ->setDescription('Generate stats');
    }

    protected function execute(InputInterface $input, OutputInterface $output) {
        dump($this->influx->getDatabase()->query("SELECT MEAN(normal) FROM user_counts")->current()->mean);
    }
}