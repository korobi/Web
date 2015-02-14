<?php

namespace Korobi\WebBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface {

    public function getConfigTreeBuilder() {
        $treeBuilder = new TreeBuilder();
        $root = $treeBuilder->root('korobi_web');
        $root->children()->arrayNode("navigation")->children()->arrayNode("items")->prototype("array")
        ->children()->scalarNode("title")->end()->scalarNode("route")->end()->scalarNode("type")->defaultValue("primary")
        ->end()->booleanNode("requires_auth")->defaultValue(false)->end()->booleanNode("requires_admin")->defaultValue(false)->end()->end()->end();
        return $treeBuilder;
    }
}
