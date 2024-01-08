<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\Coverage;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class CoverageTest extends TestCase
{
    #[Test]
    public function testGetPath(): void
    {
        $temporaryDirectory = implode(DIRECTORY_SEPARATOR, [
            dirname(__DIR__, 2),
            '.temp',
            'coverage',
        ]);

        $this->assertSame($temporaryDirectory, Coverage::getPath());
    }

    #[Test]
    public function testUsingXdebug(): void
    {
        $this->assertTrue(Coverage::usingXdebug());
    }
}
