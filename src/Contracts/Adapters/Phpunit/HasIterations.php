<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;

use NunoMaduro\Collision\Adapters\Phpunit\Iteration;

/**
 * @internal
 */
interface HasIterations
{
    public function getIteration(): Iteration;
}
