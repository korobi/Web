<?php

namespace Korobi\WebBundle\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use Symfony\Component\Yaml\Yaml;

class KorobiWebExtension extends Extension {

    public function load(array $config, ContainerBuilder $container) {
        $config = array_merge(
            $config, // base configuration
            Yaml::parse(file_get_contents(__DIR__ . '/../Resources/config/korobi.yml'))
        );

        $configuration = new Configuration();
        $config = $this->processConfiguration($configuration, $config);

        $loader = new YamlFileLoader($container, new FileLocator(__DIR__ . '/../Resources/config'));
        $loader->load('services.yml');

        $container->setParameter('korobi.config', $config);
    }
}
