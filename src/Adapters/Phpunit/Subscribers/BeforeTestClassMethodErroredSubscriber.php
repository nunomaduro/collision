<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\BeforeFirstTestMethodErroredSubscriber;

/**
 * @internal
 */
final class BeforeTestClassMethodErroredSubscriber extends Subscriber implements BeforeFirstTestMethodErroredSubscriber
{
    public function notify(BeforeFirstTestMethodErrored $event): void
    {
        throw new ShouldNotHappen();
    }
}
