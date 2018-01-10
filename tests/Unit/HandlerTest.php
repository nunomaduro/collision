<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use NunoMaduro\Collision\Writer;
use NunoMaduro\Collision\Handler;
use Symfony\Component\Console\Output\ConsoleOutput;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;
use NunoMaduro\Collision\Contracts\Handler as HandlerContract;

class HandlerTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(HandlerContract::class, new Handler());
    }

    /** @test */
    public function it_handles_an_exception(): void
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
    public function it_sets_the_output(): void
    {
        $writerMock = $this->createMock(WriterContract::class);
        $output = new ConsoleOutput();

        $writerMock->expects($this->once())
            ->method('setOutput')
            ->with($output);

        (new Handler($writerMock))->setOutput($output);
    }

    /** @test */
    public function it_gets_the_writer(): void
    {
        $writer = new Writer();
        $handler = new Handler($writer);

        $this->assertEquals($handler->getWriter(), $writer);
    }
}
