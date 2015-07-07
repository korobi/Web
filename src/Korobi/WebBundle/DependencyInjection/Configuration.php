<?php

namespace Korobi\WebBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('korobi_web');
        $root->children()
            ->arrayNode('homepage_excluded_channels')
                ->prototype('array')
                    ->children()
                        ->scalarNode("channel")->end()
                        ->scalarNode("network")->end()
                    ->end()
                ->end()
            ->end()
            ->arrayNode('navigation')->children()
                ->arrayNode('items')->prototype('array')
                    ->children()
                        ->scalarNode('title')->end()
                        ->arrayNode('routes')
                            ->beforeNormalization()
                                ->ifTrue(function($v) { return $v === null; })
                                ->then(function($v) { return []; })
                            ->end()
                            ->prototype('scalar')->end()
                        ->end()
                        ->scalarNode('route')->end()
                        ->scalarNode('type')->defaultValue('primary')->end()
                        ->booleanNode('requires_auth')->defaultValue(false)->end()
                        ->booleanNode('requires_admin')->defaultValue(false)->end()
                        ->booleanNode('external')->defaultValue(false)->end()
                    ->end()
                ->end();
        return $treeBuilder;
    }
}
