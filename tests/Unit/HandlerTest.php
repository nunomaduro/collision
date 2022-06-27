<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\Contracts\Handler as HandlerContract;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;

class HandlerTest extends TestCase
{
    /** @test */
    public function itRespectsIsContract(): void
    {
        $this->assertInstanceOf(HandlerContract::class, new Handler());
    }

    /** @test */
    public function itHandlesAnException(): void
    {
        $writerMock = $this->createMock(WriterContract::class);
        $inspectorMock = $this->createMock(\Whoops\Exception\Inspector::class);

        $writerMock->expects($this->once())
            ->method('write')
            ->with($inspectorMock);

        $handler = new Handler($writerMock);

        $handler->setInspector($inspectorMock);

        $this->assertEquals($handler->handle(), Handler::QUIT);
    }

    /** @test */
    public function itSetsTheOutput(): void
    {
        $writerMock = $this->createMock(WriterContract::class);
        $output = new ConsoleOutput();

        $writerMock->expects($this->once())
            ->method('setOutput')
            ->with($output);

        (new Handler($writerMock))->setOutput($output);
    }

    /** @test */
    public function itGetsTheWriter(): void
    {
        $writer = new Writer();
        $handler = new Handler($writer);

        $this->assertEquals($handler->getWriter(), $writer);
    }
}
