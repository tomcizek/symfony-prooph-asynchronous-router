<?php

declare(strict_types=1);

namespace TomCizek\AsynchronousRouter\Tests;

use PHPUnit\Framework\TestCase;
use Symfony\Component\DependencyInjection\ContainerBuilder;
use TomCizek\AsynchronousRouter\ProophAsynchronousRouterBundle;

class BundleTest extends TestCase
{
    /**
     * @test
     */
    public function it_builds_bundle()
    {
        $container = new ContainerBuilder();
        $bundle = new ProophAsynchronousRouterBundle();
        $bundle->build($container);
        self::assertTrue(true);
    }
}
