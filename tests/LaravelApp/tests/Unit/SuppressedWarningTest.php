<?php

namespace Tests\Unit;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\TestCase;

error_reporting(E_ALL);

#[Group('suppressed-warning')]
class SuppressedWarningTest extends TestCase
{
    public function test_suppressed_warning_example()
    {
        @file_get_contents(__DIR__.'/missing-fixture-file');

        $this->assertTrue(true);
    }

    public function test_unsuppressed_warning_example()
    {
        file_get_contents(__DIR__.'/missing-fixture-file');

        $this->assertTrue(true);
    }
}
