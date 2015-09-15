<?php

namespace Korobi\WebBundle;

use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\HttpKernel\Bundle\Bundle;

class KorobiWebBundle extends Bundle {

    public function build(ContainerBuilder $container) {
        $realRootDir = realpath($container->getParameter("kernel.root_dir") . DIRECTORY_SEPARATOR . ".." . DIRECTORY_SEPARATOR);
        $container->setParameter("korobi.real_root_dir", $realRootDir);
    }
}
