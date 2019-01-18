<?php

namespace Ae\FeatureBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * {@inheritdoc}
 */
class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('ae_feature');

        $rootNode
            ->children()
                ->scalarNode('cache')
                    ->defaultValue('ae_feature.default_cache')
                ->end()
                ->scalarNode('provider_key')
                    ->defaultValue('main')
                    ->cannotBeEmpty()
                ->end()
            ->end();

        return $treeBuilder;
    }
}
