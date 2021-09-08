<?php

declare(strict_types=1);

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
     * @var int
     */
    public $iteration;

    /**
     * The total number of times the test will be run.
     *
     * @readonly
     *
     * @var int
     */
    public $total;

    public function __construct(int $iteration, int $total)
    {
        $this->iteration       = $iteration;
        $this->total           = $total;
    }
}
