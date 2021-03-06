<?php

namespace Dark\RedisListBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode = $treeBuilder->root('dark_redis_list');

        $rootNode
            ->children()
                ->scalarNode('collector')->defaultValue('single')->end()
                ->scalarNode('template')->defaultValue('DarkRedisListBundle:Pagination:list.html.twig')->end()
                ->scalarNode('time')->defaultNull()->end()
                ->scalarNode('use_listener')->defaultValue(true)->end()
            ->end()
        ;

        return $treeBuilder;
    }
}
