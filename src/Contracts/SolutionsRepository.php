<?php

/*
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Contracts;

use Facade\IgnitionContracts\Solution;
use Throwable;

/**
 * This is an Collision Solutions Repository contract.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
interface SolutionsRepository
{
    /**
     * Gets the solutions from the given `$throwable`.
     *
     * @return array<int, Solution>
     */
    public function getFromThrowable(Throwable $throwable): array;
}
