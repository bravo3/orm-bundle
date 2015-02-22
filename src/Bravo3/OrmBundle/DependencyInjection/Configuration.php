<?php

namespace Bravo3\OrmBundle\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

/**
 * This is the class that validates and merges configuration from your app/config files
 *
 * To learn more see {@link http://symfony.com/doc/current/cookbook/bundles/extension.html#cookbook-bundles-extension-config-class}
 */
class Configuration implements ConfigurationInterface
{
    /**
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder()
    {
        $treeBuilder = new TreeBuilder();
        $rootNode    = $treeBuilder->root('orm');

        $rootNode
            ->children()
            ->scalarNode('user_class')
                ->defaultValue('Bravo3\OrmBundle\Entity')
                ->end()
            ->scalarNode('auth_firewall')
                ->defaultValue('main')
                ->end()
            ->arrayNode('params')
                ->prototype('variable')->end()
                ->end()
            ->arrayNode('options')
                ->prototype('variable')->end()
                ->end()
            ->arrayNode('sessions')
                ->addDefaultsIfNotSet()
                ->children()
                    ->scalarNode('entity')->cannotBeEmpty()->defaultValue('Bravo3\OrmBundle\Entity\Session')->end()
                    ->integerNode('ttl')->min(1)->defaultValue(3600)->end()
                    ->end()
                ->end()
            ->arrayNode('user_roles')
                ->isRequired()
                ->requiresAtLeastOneElement()
                ->prototype('scalar')->end()
                ->end();

        return $treeBuilder;
    }
}
