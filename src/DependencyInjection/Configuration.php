<?php

declare(strict_types = 1);

namespace TomCizek\AsynchronousRouter\DependencyInjection;

use Symfony\Component\Config\Definition\Builder\ArrayNodeDefinition;
use Symfony\Component\Config\Definition\Builder\NodeDefinition;
use Symfony\Component\Config\Definition\Builder\TreeBuilder;
use Symfony\Component\Config\Definition\ConfigurationInterface;

final class Configuration implements ConfigurationInterface
{
    /**
     * Normalizes XML config and defines config tree
     * {@inheritdoc}
     */
    public function getConfigTreeBuilder(): TreeBuilder
    {
        $treeBuilder = new TreeBuilder();
        /** @var ArrayNodeDefinition $rootNode Help phpstan */
        $rootNode = $treeBuilder->root('prooph_asynchronous_messaging');
        $rootNode
            ->fixXmlConfig('producer', 'producers')
            ->children()
                ->arrayNode('producers')
                ->useAttributeAsKey('name')
                    ->arrayPrototype()
                        ->children()
                            ->append($this->createBridgeNode())
                            ->append($this->createRoutesNode())
                        ->end()
                    ->end()
                ->end()
            ->end();

        return $treeBuilder;
    }

    private function createRoutesNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        /** @var ArrayNodeDefinition $routesNode */
        $routesNode = $treeBuilder->root('routes');
        $routesNode->useAttributeAsKey('message');
        $routingKeyNode = $routesNode->scalarPrototype();
        $routingKeyNode->end();

        return $routesNode;
    }

    private function createBridgeNode(): NodeDefinition
    {
        $treeBuilder = new TreeBuilder();
        /** @var ArrayNodeDefinition $bridgeNode */
        $bridgeNode = $treeBuilder->root('bridge', 'scalar');
        $bridgeNode
            ->beforeNormalization()
            ->ifTrue(
                function ($v) {
                    return strpos($v, '@') === 0;
                }
            )
            ->then(
                function ($v) {
                    return substr($v, 1);
                }
            )
            ->end();

        return $bridgeNode;
    }
}
