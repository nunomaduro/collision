<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\Coverage;
use PHPUnit\Framework\TestCase;

class CoverageTest extends TestCase
{
    /** @test */
    public function testGetPath(): void
    {
        $this->assertSame(dirname(__DIR__, 2).'/'.'.temp/coverage', Coverage::getPath());
    }

    /** @test */
    public function testIsAvailable(): void
    {
        $this->assertTrue(Coverage::isAvailable());
    }

    /** @test */
    public function testUsingXdebug(): void
    {
        $this->assertTrue(Coverage::usingXdebug());
    }
}
