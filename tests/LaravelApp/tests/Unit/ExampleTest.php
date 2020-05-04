<?php

declare(strict_types=1);

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;

class ExampleTest extends TestCase
{
    /**
     * @group fail
     */
    public function testFailExample()
    {
        $this->assertFalse(true);
    }

    public function testBasicTest()
    {
        $this->assertTrue(true);
    }
}
