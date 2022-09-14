<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\FailedSubscriber;

/**
 * @internal
 */
final class TestFailedSubscriber extends Subscriber implements FailedSubscriber
{
    public function notify(Failed $event): void
    {
        $this->printer()->testFailed($event);
    }
}
