<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit;

/**
 * @internal
 */
final class Timer
{
    /**
     * @var float
     */
    private $start;

    /**
     * Timer constructor.
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
