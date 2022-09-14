<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\DeprecationTriggeredSubscriber;

/**
 * @internal
 */
final class TestTriggeredPhpDeprecationSubscriber extends Subscriber implements DeprecationTriggeredSubscriber
{
    public function notify(DeprecationTriggered $event): void
    {
        $this->printer()->testDeprecationTriggered($event);
    }
}
