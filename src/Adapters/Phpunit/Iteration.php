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

    public function __construct(?int $iteration = null, ?int $totalIterations = null)
    {
        $this->iteration       = $iteration;
        $this->totalIterations = $totalIterations;
    }

    public function isValid(): bool
    {
        return $this->totalIterations !== null && $this->iteration !== null;
    }
}
