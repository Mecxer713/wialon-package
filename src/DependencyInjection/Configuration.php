<?php

namespace Mecxer\WialonPackage\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

class Configuration implements ConfigurationInterface
{
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder('wialon');
        $rootNode = $treeBuilder->getRootNode();
        /** @var ArrayNodeDefinition $rootNode */
        $rootNode
            ->children()
            ->scalarNode('token')
            ->isRequired()
            ->cannotBeEmpty()
            ->info('Votre token Wialon')
            ->end()
            ->scalarNode('base_url')
            ->defaultValue('https://hst-api.wialon.com/wialon/ajax.html')
            ->info('URL de base de l\'API')
            ->end()
            ->end();

        return $treeBuilder;
    }
}
