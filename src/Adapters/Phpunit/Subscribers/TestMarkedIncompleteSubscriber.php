<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\MarkedIncompleteSubscriber;

/**
 * @internal
 */
final class TestMarkedIncompleteSubscriber extends Subscriber implements MarkedIncompleteSubscriber
{
    public function notify(MarkedIncomplete $event): void
    {
        $this->printer()->testMarkedIncomplete($event);
    }
}
