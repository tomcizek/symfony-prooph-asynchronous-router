# tomcizek/symfony-prooph-asynchronous-router

[![Build Status](https://img.shields.io/travis/tomcizek/symfony-prooph-asynchronous-router.svg?style=flat-square)](https://travis-ci.org/tomcizek/symfony-prooph-asynchronous-router)
[![Quality Score](https://img.shields.io/scrutinizer/g/tomcizek/symfony-prooph-asynchronous-router.svg?style=flat-square)](https://scrutinizer-ci.com/g/tomcizek/symfony-prooph-asynchronous-router)
[![Code Coverage](https://img.shields.io/scrutinizer/coverage/g/tomcizek/symfony-prooph-asynchronous-router.svg?style=flat-square)](https://scrutinizer-ci.com/g/tomcizek/symfony-prooph-asynchronous-router)

Symfony Bundle for easy routing of asynchronous messages.

## Why bother?

It allows you to 
<ol>
	<li>
		Automatically create MessageProducer services.
	</li>
	<li>
	    Define routes for each service in yaml or xml config.
	</li>
	<li>
		Define AsynchronousMessageProducerBridge for each service, which you can implement using any infrastructure.
	</li>
</ol>

# Quick start

### 1) Install this library through composer
`composer require tomcizek/symfony-prooph-asynchronous-router`

### 2) Register these Bundles in your kernel (individual, but might be config/bundles.php)
```php

return [
    Prooph\Bundle\ServiceBus\ProophServiceBusBundle::class => ['all' => true],
    TomCizek\AsynchronousRouter\ProophAsynchronousRouterBundle::class => ['all' => true],
];

```

### 3) Implement your own TomCizek\AsynchronousRouter\AsynchronousMessageProducerBridge
Simplest working example implementation for RabbitMq
<a href="https://github.com/php-amqplib/RabbitMqBundle">
    php-amqplib
</a> infrastructure might look like:
```php
<?php declare(strict_types = 1);

namespace YourApp\Infrastructure\Rabbit;

use DateTime;
use OldSound\RabbitMqBundle\RabbitMq\Producer;
use Prooph\Common\Messaging\Message;
use Prooph\Common\Messaging\MessageConverter;
use Prooph\Common\Messaging\MessageDataAssertion;
use TomCizek\AsynchronousRouter\AsynchronousMessageProducerBridge;

final class RabbitAsynchronousMessageProducerBridge implements AsynchronousMessageProducerBridge
{
	/** @var Producer */
	private $producer;

	/** @var MessageConverter */
	private $messageConverter;

	public function __construct(Producer $producer, MessageConverter $messageConverter)
	{
		$this->producer = $producer;
		$this->messageConverter = $messageConverter;
	}

	public function publishWithRoutingKey(Message $message, string $routingKey): void
	{
		$stringMessage = $this->convertMessageToString($message);
		$this->producer->publish($stringMessage, $routingKey);
	}

	private function convertMessageToString(Message $message): string
	{
		$messageData = $this->messageConverter->convertToArray($message);
		MessageDataAssertion::assert($messageData);
		$messageData['created_at'] = $message->createdAt()->format(DateTime::ATOM);

		return json_encode($messageData);
	}
}

```

### 4) Setup your configuration for prooph components in your symfony *.yml config!

```yaml
prooph_asynchronous_router:
    producers:
        # this key defines service id of created producer, this will create 'prooph_asynchronous_router.firstProducer'
        firstProducer:
            # bridge refers to service with your implementation of AsynchronousMessageProducerBridge
            bridge: 'firstAsynchronousMessageProducerBridge'
            # Routes are defined that key is message name and value is routing key. 
            # AsynchronousMessageProducerBridge will then recieve message instance and routing key string 
            # in publishWithRoutingKey method. 
            routes:
                App\Namespace\FirstMessage: first_routing_key

# this is standard service-bus-symfony-bundle configuration
prooph_service_bus:
    event_buses:
        first_event_bus:
            router:
                # here you refer to automatically created service you defined few lines above.
                async_switch: 'prooph_asynchronous_router.firstProducer'
services:
    # here you register your implementation of AsynchronousMessageProducerBridge to which we refer above.
    firstAsynchronousMessageProducerBridge:
        class: TomCizek\AsynchronousRouter\Tests\DependencyInjection\Fixture\Model\FirstAsynchronousMessageProducerBridge

```

<a href="https://github.com/tomcizek/symfony-prooph-asynchronous-router/blob/master/docs/configuration_example.md">
    Another configuration example
</a>

## Contribute

Please feel free to fork and extend existing or add new features and send a pull request with your changes! 
To establish a consistent code quality, please provide unit tests for all your changes and may adapt the documentation.
