<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\Contracts\Provider as ProviderContract;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider;
use PHPUnit\Framework\TestCase;
use Whoops\Run;
use Whoops\RunInterface;

class ProviderTest extends TestCase
{
    /** @test */
    public function itRespectsIsContract(): void
    {
        $this->assertInstanceOf(ProviderContract::class, new Provider());
    }

    /** @test */
    public function itRegistersTheErrorHandler(): void
    {
        $handler = new Handler();

        $runMock = $this->createMock(RunInterface::class);

        $runMock->expects($this->once())
            ->method('pushHandler')
            ->with($handler)
            ->willReturn($runMock);

        $runMock->expects($this->once())
            ->method('register');

        (new Provider($runMock, $handler))->register();
    }

    /** @test */
    public function itGetsTheHandler(): void
    {
        $handler = new Handler();
        $provider = new Provider(new Run(), $handler);

        $this->assertEquals($provider->getHandler(), $handler);
    }
}
