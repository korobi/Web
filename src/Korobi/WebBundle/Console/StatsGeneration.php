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
        dump($this->influx->getDatabase()->query("SELECT MEAN(normal) FROM user_counts WHERE channel='#drtshock'")->current()->mean);
        $data = $this->influx->getDatabase()->query("SELECT total FROM user_counts  WHERE channel='#drtshock' GROUP BY TIME(10m)");
        $distinctChannels = $this->influx->getDatabase()->query("SELECT DISTINCT(channel) FROM user_counts");
        //dump($distinctChannels);
        //dump($data);
        $runningTotal = 0;
        $lastItem = $data->current()->total;
        $i = 0;
        foreach ($data as $item) {
            if ($i === 0) {
                $i = 1;
                continue;
            }
            $runningTotal += $item->total - $lastItem;
            $lastItem = $item->total;
        }
        $mean = $runningTotal / ($data->count()-1);

        // p(X = x) = mean^x * e^(-mean) all over x factorial

        $atleastTenUsers = 0;
        $output->writeln("Approximating as possion with parameter $mean");
        $output->writeln("Let X = the net gain in users per a 10 minute period");
        $output->writeln("Working out X <= 9...");
        //computes x <= 9, therefore x >= 10 = 1- x <= 9
        for ($i = 9; $i != 0; $i--) {
            $val = pow($mean, $i) * (1 / exp($mean));
            $fact = 1;
            for ($z = $i; $z != 1; $z--) {
                $fact *= $z;
            }
            $val = $val / $fact;
            $output->writeln("($mean ^ $i * e^(-$mean)) / $i! = $val");

            $atleastTenUsers += $val;
        }
        $atleastTenUsers = 1 - $atleastTenUsers;
        $output->writeln("Working out 1 - X <= 9 which is equivalent to X >= 10");

        $output->writeln("The probability of at least ten users joining in a ten minute period is " . ($atleastTenUsers));

    }
}