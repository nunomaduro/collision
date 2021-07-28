<?php

declare(strict_types=1);

namespace Tests\Feature;

use NunoMaduro\Collision\Adapters\Phpunit\Iteration;
use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasIterations;
use Tests\TestCase;

class ExampleWithIterationTest extends TestCase implements HasIterations
{
    /**
     * @group iterations
     */
    public function testPassExample()
    {
        $this->assertTrue(true);
    }

    public function getIteration(): Iteration
    {
        return new Iteration(1, 10);
    }
}
