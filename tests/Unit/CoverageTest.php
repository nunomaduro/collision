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
        $temporaryDirectory = implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__, 2),
            '.temp',
            'coverage',
        ]);

        $this->assertSame($temporaryDirectory, Coverage::getPath());
    }
}
