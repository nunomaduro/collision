<?php

namespace NunoMaduro\Collision\Adapters\Phpunit;

/**
 * @internal
 */
class Iteration
{
    /**
     * The current iteration of a repeated test.
     *
     * @readonly
     *
     * @var int|null
     */
    public $iteration = null;

    /**
     * The total number of times the test will be run.
     *
     * @readonly
     *
     * @var int|null
     */
    public $totalIterations = null;

    public function __construct(?int $iteration, ?int $totalIterations)
    {
        $this->iteration = $iteration;
        $this->totalIterations = $totalIterations;
    }
}
