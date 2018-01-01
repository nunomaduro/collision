<?php

namespace Tests\Unit;

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
}