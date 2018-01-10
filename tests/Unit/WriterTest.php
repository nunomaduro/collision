<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use Whoops\Exception\Inspector;
use NunoMaduro\Collision\Writer;
use Tests\FakeProgram\HelloWorldFile1;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\OutputInterface;
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
    public function it_writes_the_exception(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        $output = new BufferedOutput();

        (new Writer($output))->write($inspector);

        $projectDir = dirname(__DIR__);

        $result = <<<EOF

   Tests\FakeProgram\FakeException  : Fail description

  at $projectDir/FakeProgram/HelloWorldFile3.php: 9
  5: class HelloWorldFile3
  6: {
  7:     public static function say()
  8:     {
  9:         return new FakeException('Fail description');
  10:     }
  11: }
  12: 

  Exception trace:

  1   Tests\FakeProgram\HelloWorldFile3::say()
      $projectDir/FakeProgram/HelloWorldFile2.php : 9

  2   Tests\FakeProgram\HelloWorldFile2::say()
      $projectDir/FakeProgram/HelloWorldFile1.php : 9

  Please use the argument -v to see more details.

EOF;

        $this->assertEquals($output->fetch(), $result);
    }

    /** @test */
    public function it_writes_details(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($output = new BufferedOutput())->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        (new Writer($output))->write($inspector);

        $projectDir = dirname(__DIR__);

        $result = <<<EOF

   Tests\FakeProgram\FakeException  : Fail description

  at $projectDir/FakeProgram/HelloWorldFile3.php: 9
  5: class HelloWorldFile3
  6: {
  7:     public static function say()
  8:     {
  9:         return new FakeException('Fail description');
  10:     }
  11: }
  12: 

  Exception trace:

  1   Tests\FakeProgram\HelloWorldFile3::say()
      $projectDir/FakeProgram/HelloWorldFile2.php : 9

  2   Tests\FakeProgram\HelloWorldFile2::say()
      $projectDir/FakeProgram/HelloWorldFile1.php : 9

  3   Tests\FakeProgram\HelloWorldFile1::say()
      $projectDir/Unit/WriterTest.php :
EOF;

        $this->assertContains($result, $output->fetch());
    }

    /** @test */
    public function it_ignores_folders(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        $output = new BufferedOutput();

        (new Writer($output))->ignoreFilesIn(['*/FakeProgram/*'])->write($inspector);

        $projectDir = dirname(__DIR__);

        $result = <<<EOF

   Tests\FakeProgram\FakeException  : Fail description

  at $projectDir/Unit/WriterTest.php
EOF;

        $this->assertContains($result, $output->fetch());
    }

    /** @test */
    public function it_hides_editor(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        $output = new BufferedOutput();

        (new Writer($output))->showEditor(false)->write($inspector);

        $projectDir = dirname(__DIR__);

        $result = <<<EOF

   Tests\FakeProgram\FakeException  : Fail description

  Exception trace:

  1   Tests\FakeProgram\HelloWorldFile3::say()
      $projectDir/FakeProgram/HelloWorldFile2.php : 9

  2   Tests\FakeProgram\HelloWorldFile2::say()
      $projectDir/FakeProgram/HelloWorldFile1.php : 9

  Please use the argument -v to see more details.

EOF;

        $this->assertContains($result, $output->fetch());
    }

    /** @test */
    public function it_hides_trace(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        $output = new BufferedOutput();

        (new Writer($output))->showTrace(false)->write($inspector);

        $projectDir = dirname(__DIR__);

        $result = <<<EOF

   Tests\FakeProgram\FakeException  : Fail description

  at $projectDir/FakeProgram/HelloWorldFile3.php: 9
  5: class HelloWorldFile3
  6: {
  7:     public static function say()
  8:     {
  9:         return new FakeException('Fail description');
  10:     }
  11: }
  12: 

EOF;

        $this->assertContains($result, $output->fetch());
    }
}
