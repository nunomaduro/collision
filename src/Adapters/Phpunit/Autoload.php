<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Adapters\Phpunit\Subscribers\EnsurePrinterIsRegisteredSubscriber;

if (class_exists(EnsurePrinterIsRegisteredSubscriber::class)) {
    EnsurePrinterIsRegisteredSubscriber::register();
}
