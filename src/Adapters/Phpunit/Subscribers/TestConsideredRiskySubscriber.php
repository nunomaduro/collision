<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\ConsideredRiskySubscriber;

/**
 * @internal
 */
final class TestConsideredRiskySubscriber extends Subscriber implements ConsideredRiskySubscriber
{
    public function notify(ConsideredRisky $event): void
    {
        $this->printer()->testConsideredRisky($event);
    }
}
