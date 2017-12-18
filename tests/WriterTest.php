<?php

namespace Tests;

use PHPUnit\Framework\TestCase;
use Whoops\Exception\Inspector;
use NunoMaduro\Collision\Writer;
use Whoops\Exception\FrameCollection;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;

class WriterTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(WriterContract::class, new Writer());
    }

    /** @test */
    public function it_gets_the_output(): void
    {
        $writer = new Writer($output = new ConsoleOutput());

        $this->assertEquals($writer->getOutput(), $output);
    }

    /** @test */
    public function it_sets_the_output(): void
    {
        $writer = (new Writer())->setOutput($output = new ConsoleOutput());

        $this->assertEquals($writer->getOutput(), $output);
    }

    /** @test */
    public function it_writes_the_title(): void
    {
        $inspectorMock = $this->createMock(Inspector::class);
        $output = new BufferedOutput();

        $exception = new \Exception('This is a description of the error');

        $inspectorMock->expects($this->once())
            ->method('getFrames')
            ->willReturn(new FrameCollection([]));
        $inspectorMock->expects($this->any())
            ->method('getException')
            ->willReturn($exception);
        $inspectorMock->expects($this->once())
            ->method('getExceptionName')
            ->willReturn('ExceptionClass');

        (new Writer($output))->write($inspectorMock);

        $result = $output->fetch();
        $this->assertContains('ExceptionClass', $result);
        $this->assertContains('This is a description of the error', $result);
    }

    /** @test */
    public function it_writes_the_editor(): void
    {
        $this->assertTrue(true); // @todo
    }

    /** @test */
    public function it_writes_the_trace(): void
    {
        $this->assertTrue(true); // @todo
    }
}
