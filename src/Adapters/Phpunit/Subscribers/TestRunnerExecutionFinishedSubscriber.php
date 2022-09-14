<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionFinishedSubscriber;

/**
 * @internal
 */
final class TestRunnerExecutionFinishedSubscriber extends Subscriber implements ExecutionFinishedSubscriber
{
    public function notify(ExecutionFinished $event): void
    {
        $this->printer()->testRunnerExecutionFinished($event);
    }
}
