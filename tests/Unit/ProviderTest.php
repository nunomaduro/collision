<?php

namespace Tests\Unit;

use Whoops\Run;
use Whoops\RunInterface;
use PHPUnit\Framework\TestCase;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider;
use NunoMaduro\Collision\Contracts\Provider as ProviderContract;

class ProviderTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(ProviderContract::class, new Provider());
    }

    /** @test */
    public function it_registers_the_error_handler(): void
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
    public function it_gets_the_handler(): void
    {
        $handler = new Handler();
        $provider = new Provider(new Run(), $handler);

        $this->assertEquals($provider->getHandler(), $handler);
    }
}
