<?php

namespace Tests\Unit\Adapters\Laravel\LaravelFurtherDetailWriter;

use Exception;
use NunoMaduro\Collision\Adapters\Laravel\LaravelFurtherDetailWriter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;

class IgnoredExceptionTest extends TestCase
{

    /** @test */
    public function itPrintsNothingForUnhandledExceptions()
    {
        $output = new BufferedOutput();
        $writer = new LaravelFurtherDetailWriter();

        $writer->write($output, new Exception());

        $this->assertEquals($output->fetch(), '');
    }

}
