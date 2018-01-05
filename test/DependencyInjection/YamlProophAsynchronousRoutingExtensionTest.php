<?php

declare(strict_types=1);

namespace TomCizek\AsynchronousRouter\Tests\DependencyInjection;

use Symfony\Component\Config\FileLocator;
use Symfony\Component\DependencyInjection\ContainerBuilder as SymfonyContainerBuilder;
use Symfony\Component\DependencyInjection\Loader\YamlFileLoader;

class YamlProophAsynchronousRoutingExtensionTest extends AbstractProophAsynchronousRoutingExtensionTestCase
{
    protected function buildContainer(): TestContainerBuilder
    {
        return TestContainerBuilder::buildContainer(function (SymfonyContainerBuilder $container) {
            return new YamlFileLoader($container, new FileLocator(__DIR__.'/Fixture/config/yml'));
        }, 'yml');
    }
}
