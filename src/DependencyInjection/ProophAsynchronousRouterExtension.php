<?php

declare(strict_types = 1);

namespace TomCizek\AsynchronousRouter\DependencyInjection;

use Prooph\Common\Messaging\MessageConverter;
use Symfony\Component\Config\Definition\ConfigurationInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Definition;
use Symfony\Component\DependencyInjection\Reference;
use Symfony\Component\HttpKernel\DependencyInjection\Extension;
use TomCizek\AsynchronousRouter\AsynchronousMessageProducer;

/**
 * Defines and load message bus instances.
 */
final class ProophAsynchronousRouterExtension extends Extension
{
    const KEY_BRIDGE = 'bridge';
    const KEY_ROUTES = 'routes';

    public function getConfiguration(array $config, ContainerBuilder $container): ConfigurationInterface
    {
        return new Configuration();
    }

    public function load(array $configs, ContainerBuilder $container)
    {
        $configuration = $this->getConfiguration($configs, $container);
        $config = $this->processConfiguration($configuration, $configs);

        if (! empty($config['producers'])) {
            $this->loadProducers($config['producers'], $container);
        }
    }

    private function loadProducers(
        array $config,
        ContainerBuilder $container
    ): void {
        $producers = [];
        foreach (array_keys($config) as $producerName) {
            $producers[$producerName] = 'prooph_asynchronous_router.' . $producerName;
        }
        $container->setParameter('prooph_asynchronous_router.producers', $producers);

        foreach ($config as $producerName => $producerConfig) {
            $this->loadProducer($producerName, $config[$producerName], $container);
        }
    }

    private function loadProducer(string $name, array $config, ContainerBuilder $container)
    {
        $routes = is_array($config[self::KEY_ROUTES]) ? $config[self::KEY_ROUTES] : [];

        $producerBridgeKey = $config[self::KEY_BRIDGE];

        $asynchronousProducerDefinition = new Definition(AsynchronousMessageProducer::class);
        $asynchronousProducerDefinition
            ->setArguments(
                [
                    new Reference($producerBridgeKey),
                    new Reference(MessageConverter::class),
                ]
            )
            ->addMethodCall('injectRoutes', [$routes])
        ->setPublic(true);
        $producerServiceId = 'prooph_asynchronous_router.' . $name;

        $container->setDefinition($producerServiceId, $asynchronousProducerDefinition);
    }
}
