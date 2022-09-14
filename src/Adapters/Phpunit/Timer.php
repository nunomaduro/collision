<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

/**
 * @internal
 */
final class Timer
{
    private float $start;

    /**
     * Crates a new Timer instance.
     */
    private function __construct(float $start)
    {
        $this->start = $start;
    }

    /**
     * Starts the timer.
     */
    public static function start(): Timer
    {
        return new self(microtime(true));
    }

    /**
     * Returns the elapsed time in microseconds.
     */
    public function result(): float
    {
        return microtime(true) - $this->start;
    }
}
