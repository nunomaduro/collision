<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\PreparedSubscriber;

/**
 * @internal
 */
final class TestPreparedSubscriber extends Subscriber implements PreparedSubscriber
{
    public function notify(Prepared $event): void
    {
        $this->printer()->testPrepared($event);
    }
}
