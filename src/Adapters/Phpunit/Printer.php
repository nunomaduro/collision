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
 * This `if` condition exists because phpunit
 * is not a direct dependency of Collision.
 *
 * This code bellow it's for phpunit@8
 */
if (class_exists(\PHPUnit\Runner\Version::class) && intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) === 8) {

    /**
     * This is an Collision Phpunit Adapter implementation.
     *
     * @internal
     */
    final class Printer extends \PHPUnit\Util\Printer implements \PHPUnit\Framework\TestListener
    {
        use PrinterContents;
    }
}

/**
 * This `if` condition exists because phpunit
 * is not a direct dependency of Collision.
 *
 * This code bellow it's for phpunit@9
 */
if (class_exists(\PHPUnit\Runner\Version::class) && intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) === 9) {

    /**
     * This is an Collision Phpunit Adapter implementation.
     *
     * @internal
     */
    final class Printer implements \PHPUnit\TextUI\ResultPrinter
    {
        use PrinterContents;

        /**
         * Intentionally left blank as we output things on events of the listener.
         *
         * @param  \PHPUnit\Framework\TestResult $result
         *
         * @return void
         */
        public function printResult(\PHPUnit\Framework\TestResult $result): void
        {
            // ..
        }
    }
}
