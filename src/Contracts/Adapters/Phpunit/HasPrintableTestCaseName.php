<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;

/**
 * @internal
 */
interface HasPrintableTestCaseName
{
    /**
     * Returns the test case name that should be used by the printer.
     */
    public function getPrintableTestCaseName(): string;
}
