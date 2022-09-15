<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts\Adapters\Phpunit;

/**
 * @internal
 */
interface HasPrintableTestCaseName
{
    /**
     * Returns the test case name that should be used by the printer.
     */
    public static function getPrintableTestCaseName(): string;

    /**
     * Returns the test case method name that should be used by the printer.
     */
    public static function getPrintableTestCaseMethodName(): string;
}
