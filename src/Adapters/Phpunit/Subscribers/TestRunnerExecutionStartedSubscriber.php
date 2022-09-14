<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Event\TestRunner\ExecutionStartedSubscriber;

/**
 * @internal
 */
final class TestRunnerExecutionStartedSubscriber extends Subscriber implements ExecutionStartedSubscriber
{
    public function notify(ExecutionStarted $event): void
    {
        $this->printer()->testRunnerExecutionStarted($event);
    }
}
