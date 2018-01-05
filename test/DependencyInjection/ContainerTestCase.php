<?php

declare(strict_types = 1);

namespace TomCizek\AsynchronousRouter\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Prooph\Bundle\ServiceBus\DependencyInjection\ProophServiceBusExtension;
use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;
use TomCizek\AsynchronousRouter\DependencyInjection\ProophAsynchronousRouterExtension;

abstract class ContainerTestCase extends TestCase
{
    /** @var ContainerBuilder */
    protected $containerBuilder;

    protected function givenContainerWithFixtureFiles(
        array $fixtureFiles = []
    ) {
        $container = $this->buildContainer()
            ->withExtensions(
                new ProophAsynchronousRouterExtension(),
                new ProophServiceBusExtension()
            );
        foreach ($fixtureFiles as $fixtureFile) {
            $container->withConfigFiles($fixtureFile);
        }

        $this->containerBuilder = $container->compile();
    }

    private function buildContainer(): TestContainerBuilder
    {
        return TestContainerBuilder::buildContainer(
            function (ContainerBuilder $container) {
                return new YamlFileLoader($container, new FileLocator(__DIR__ . '/Fixture/config/yml'));
            },
            'yml'
        );
    }
}
