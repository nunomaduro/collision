<?php


namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;

use NunoMaduro\Collision\Adapters\Phpunit\Iteration;

/**
 * @internal
 */
interface HasIterations
{

    public function getIteration(): Iteration;

}
