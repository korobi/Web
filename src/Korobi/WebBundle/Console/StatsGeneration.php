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
        $data = $this->influx->getDatabase()->query("SELECT total FROM user_counts GROUP BY TIME(10m)");
        //dump($data);
        $runningTotal = 0;
        $lastItem = 0;
        foreach ($data as $item) {
            $runningTotal += $item->total - $lastItem;
            $lastItem = $item->total;
        }
        $mean = $runningTotal / $data->count();

        // p(X = x) = mean^x * e^(-mean) all over x factorial

        $atleastTenUsers = 0;
        $output->writeln("Approximating as possion with parameter $mean");
        //computes x <= 9, therefore x >= 10 = 1- x <= 9
        for ($i = 9; $i != 0; $i--) {
            $output->writeln("($mean ^ $i * e^(-$mean)) / $i!");
            $val = pow($mean, $i) * (1 / exp($mean));
            $fact = 1;
            for ($z = $i; $z != 1; $z--) {
                $fact *= $z;
            }
            $val = $val / $fact;
            $atleastTenUsers += $val;
        }
        $atleastTenUsers = 1 - $atleastTenUsers;
        dump("The probability of at least ten users joining in a ten minute period is " . ($atleastTenUsers));

    }
}