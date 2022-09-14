<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\FinishedSubscriber;

/**
 * @internal
 */
final class TestFinishedSubscriber extends Subscriber implements FinishedSubscriber
{
    public function notify(Finished $event): void
    {
        $this->printer()->testFinished($event);
    }
}
