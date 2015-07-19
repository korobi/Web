<?php

namespace Korobi\WebBundle\Console;

use Korobi\WebBundle\Util\FileCache;
use Korobi\WebBundle\Util\FileUtil;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogCacheClearCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('korobi:cache:logs:clear')
            ->setDescription('Clears the logs cache')
            ->addArgument('network', InputArgument::OPTIONAL)
            ->addArgument('channel', InputArgument::OPTIONAL);
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        $network = $in->getArgument('network');
        $channel = $in->getArgument('channel');

        $cachePath = $this->getContainer()->getParameter('korobi.config')['log_cache_directory'];

        if(!$network) {
            FileUtil::removeRecursively($cachePath);
            return;
        }

        $key = [$network];
        if($channel) {
            array_push($key, $channel);
        }

        $cache = new FileCache($cachePath);

        $count = $cache->remove($key);
        $out->writeln('Cache cleared for "' . implode(DIRECTORY_SEPARATOR, $key) . '", ' . $count . ' files and folders');
    }

}
