<?php

declare(strict_types=1);

namespace TestCaseWithStdoutOutput;

use PHPUnit\Framework\TestCase;

class OutputTest extends TestCase
{
    public function testWithOutput()
    {
        var_dump('Foo');

        $this->assertTrue(true);
    }

    public function testNothingSpecial()
    {
        // This shouldn't have any output
        $this->assertTrue(true);
    }

    public function testWithNoOutput()
    {
        $this->expectOutputRegex('/Bar/');

        var_dump('Bar');
    }
}
