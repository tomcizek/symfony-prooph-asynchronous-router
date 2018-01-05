<?php

declare(strict_types = 1);

namespace TomCizek\AsynchronousRouter\Tests\DependencyInjection;

use Prooph\ServiceBus\Exception\RuntimeException;
use React\Promise\Deferred;
use TomCizek\AsynchronousRouter\AsynchronousMessageProducer;
use TomCizek\AsynchronousRouter\Tests\DependencyInjection\Fixture\Model\FirstAsynchronousMessageProducerBridge;
use TomCizek\AsynchronousRouter\Tests\DependencyInjection\Fixture\Model\FirstMessage;

class AsynchronousMessageProducerTest extends ContainerTestCase
{
    public function testInvoke_WithRoutedMessage_ShouldPublishCorrectly()
    {
        $this->givenContainerWithFixtureFiles(
            ['commonServices', 'firstMessageRoute']
        );

        $asynchronousMessageProducer = $this->givenAsynchronousProducerFromContainer(
            'prooph_asynchronous_router.firstProducer'
        );

        $this->whenInvokeMessageProcuderWithFirstMessage($asynchronousMessageProducer);

        $this->thenMessageOfInstanceIsPublishedWithRoutingKeyOnBridge(
            FirstMessage::class,
            'first_routing_key',
            'firstAsynchronousMessageProducerBridge'
        );
    }

    public function testInvoke_WithNonRoutedMessage_ShouldThrowRuntimeException()
    {
        $this->givenContainerWithFixtureFiles(
            ['commonServices', 'secondMessageRoute']
        );

        $asynchronousMessageProducer = $this->givenAsynchronousProducerFromContainer(
            'prooph_asynchronous_router.secondProducer'
        );

        $this->expectException(RuntimeException::class);

        $this->whenInvokeMessageProcuderWithFirstMessage($asynchronousMessageProducer);
    }

    public function testInvoke_WithDefferedParam_ShouldThrowRuntimeException()
    {
        $this->givenContainerWithFixtureFiles(
            ['commonServices', 'firstMessageRoute']
        );

        $asynchronousMessageProducer = $this->givenAsynchronousProducerFromContainer(
            'prooph_asynchronous_router.firstProducer'
        );

        $this->expectException(RuntimeException::class);

        $this->whenInvokeMessageProcuderWithDeferredParam($asynchronousMessageProducer);
    }

    protected function givenAsynchronousProducerFromContainer(string $serviceId): AsynchronousMessageProducer
    {
        /** @var AsynchronousMessageProducer $asynchronousMessageProducer */
        $asynchronousMessageProducer = $this->containerBuilder->get($serviceId);

        return $asynchronousMessageProducer;
    }

    protected function whenInvokeMessageProcuderWithFirstMessage(
        AsynchronousMessageProducer $asynchronousMessageProducer
    ): void {
        $fooMessage = new FirstMessage([]);
        $asynchronousMessageProducer($fooMessage);
    }

    protected function whenInvokeMessageProcuderWithDeferredParam(
        AsynchronousMessageProducer $asynchronousMessageProducer
    ): void {
        $fooMessage = new FirstMessage([]);
        $asynchronousMessageProducer($fooMessage, new Deferred());
    }

    protected function thenMessageOfInstanceIsPublishedWithRoutingKeyOnBridge(
        string $messageInstance,
        string $routingKey,
        string $bridgeServiceId
    ): void {
        /** @var FirstAsynchronousMessageProducerBridge $testAsynchronousMessageProducerBridge */
        $testAsynchronousMessageProducerBridge = $this->containerBuilder->get($bridgeServiceId);
        $published = $testAsynchronousMessageProducerBridge->getPublished();

        self::assertCount(1, $published);
        self::assertNotEmpty($published[0]);
        self::assertEquals($routingKey, $published[0]['routingKey']);
        self::assertInstanceOf($messageInstance, $published[0]['message']);
    }
}
