<?php

declare(strict_types=1);
namespace TomCizek\AsynchronousRouter;

use Prooph\Common\Messaging\Message;

interface AsynchronousMessageProducerBridge
{
    public function publishWithRoutingKey(Message $message, string $routingKey): void;
}
