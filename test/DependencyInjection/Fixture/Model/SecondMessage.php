<?php

declare(strict_types=1);

namespace TomCizek\AsynchronousRouter\Tests\DependencyInjection\Fixture\Model;

use Prooph\Common\Messaging\Command;
use Prooph\Common\Messaging\PayloadConstructable;
use Prooph\Common\Messaging\PayloadTrait;

final class SecondMessage extends Command implements PayloadConstructable
{
    use PayloadTrait;

    private const NAME = 'App\Namespace\SecondMessage';

    public function messageName(): string
    {
        return self::NAME;
    }
}
