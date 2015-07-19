<?php

namespace Korobi\WebBundle\Console;

use Korobi\WebBundle\Util\FileCache;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;

class LogCacheStatsCommand extends ContainerAwareCommand {

    protected function configure() {
        $this
            ->setName('korobi:cache:logs:stats')
            ->setDescription('Get log cache statistics');
    }

    protected function execute(InputInterface $in, OutputInterface $out) {
        $cache = new FileCache($this->getContainer()->getParameter('korobi.config')['log_cache_directory']);
        $stats = $cache->getStats();

        $out->writeln('');
        $out->writeln('Log cache stats');
        $out->writeln('|   hits     ' . $stats['hits']);
        $out->writeln('|   misses   ' . $stats['misses']);
        $out->writeln('|   changes  ' . $stats['changes']);
        $out->writeln('');
    }

}
