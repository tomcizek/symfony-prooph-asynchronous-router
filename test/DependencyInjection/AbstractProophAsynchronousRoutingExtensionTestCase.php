<?php

declare(strict_types=1);

namespace TomCizek\AsynchronousRouter\Tests\DependencyInjection;

use PHPUnit\Framework\TestCase;
use Prooph\Bundle\ServiceBus\DependencyInjection\ProophServiceBusExtension;
use Symfony\Component\DependencyInjection\Compiler\CompilerPassInterface;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TomCizek\AsynchronousRouter\AsynchronousMessageProducer;
use TomCizek\AsynchronousRouter\DependencyInjection\ProophAsynchronousRouterExtension;

abstract class AbstractProophAsynchronousRoutingExtensionTestCase extends TestCase
{
    abstract protected function buildContainer(): TestContainerBuilder;

    public function testCanCreateProducer()
    {
        $container = $this->loadContainer('firstMessageRoute');

        $config = $container->getDefinition('prooph_asynchronous_router.firstProducer');

        self::assertEquals(AsynchronousMessageProducer::class, $config->getClass());

        /* @var $asynchronousMessageProducer AsynchronousMessageProducer */

        $asynchronousMessageProducer = $container->get('prooph_asynchronous_router.firstProducer');

        self::assertInstanceOf(AsynchronousMessageProducer::class, $asynchronousMessageProducer);
    }

    private function loadContainer($fixture, CompilerPassInterface ...$compilerPasses): ContainerBuilder
    {
        return $this->buildContainer()
            ->withExtensions(
                new ProophAsynchronousRouterExtension(),
                new ProophServiceBusExtension()
                )
            ->withConfigFiles($fixture, 'commonServices')
            ->withCompilerPasses(...$compilerPasses)
            ->compile();
    }
}
