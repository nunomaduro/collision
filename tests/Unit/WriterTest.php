<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\ConsoleColor;
use NunoMaduro\Collision\Contracts\Writer as WriterContract;
use NunoMaduro\Collision\Highlighter;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\FakeProgram\HelloWorldFile1;
use Tests\FakeProgram\HelloWorldFile4;
use Whoops\Exception\Inspector;

class WriterTest extends TestCase
{
    /** @test */
    public function itRespectsIsContract(): void
    {
        $this->assertInstanceOf(WriterContract::class, new Writer());
    }

    /** @test */
    public function itGetsTheOutput(): void
    {
        $writer = new Writer(new NullSolutionsRepository(), $output = new ConsoleOutput());

        $this->assertEquals($writer->getOutput(), $output);
    }

    /** @test */
    public function itSetsTheOutput(): void
    {
        $writer = (new Writer())->setOutput($output = new ConsoleOutput());

        $this->assertEquals($writer->getOutput(), $output);
    }

    /** @test */
    public function itWritesTheException(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->write($inspector);

        $result = <<<EOF

   Tests\FakeProgram\FakeException 

  Fail description

  at tests/FakeProgram/HelloWorldFile3.php:11
      7▕ class HelloWorldFile3
      8▕ {
      9▕     public static function say()
     10▕     {
  ➜  11▕         return new FakeException('Fail description');
     12▕     }
     13▕ }
     14▕

  1   tests/FakeProgram/HelloWorldFile2.php:11
      Tests\FakeProgram\HelloWorldFile3::say()

  2   tests/FakeProgram/HelloWorldFile1.php:11
      Tests\FakeProgram\HelloWorldFile2::say()

EOF;

        $this->assertEquals(
            $writer->getOutput()
                ->fetch(),
            $result
        );
    }

    /** @test */
    public function itWritesDetails(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        $writer = $this->createWriter();
        $writer->getOutput()->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $writer->write($inspector);

        $result = <<<EOF

   Tests\FakeProgram\FakeException 

  Fail description

  at tests/FakeProgram/HelloWorldFile3.php:11
      7▕ class HelloWorldFile3
      8▕ {
      9▕     public static function say()
     10▕     {
  ➜  11▕         return new FakeException('Fail description');
     12▕     }
     13▕ }
     14▕

  1   tests/FakeProgram/HelloWorldFile2.php:11
      Tests\FakeProgram\HelloWorldFile3::say()

  2   tests/FakeProgram/HelloWorldFile1.php:11
      Tests\FakeProgram\HelloWorldFile2::say()

  3   tests/Unit/WriterTest.php:85
      Tests\FakeProgram\HelloWorldFile1::say()
EOF;

        $this->assertStringContainsString($result, $writer->getOutput()->fetch());
    }

    /** @test */
    public function itIgnoresFolders(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->ignoreFilesIn(['*/FakeProgram/*'])
            ->write($inspector);

        $result = <<<EOF

   Tests\FakeProgram\FakeException 

  Fail description

  at tests/Unit/WriterTest.php
EOF;

        $this->assertStringContainsString(
            $result,
            $writer->getOutput()
                ->fetch()
        );
    }

    /** @test */
    public function itHidesEditor(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->showEditor(false)
            ->write($inspector);

        $result = <<<EOF

   Tests\FakeProgram\FakeException 

  Fail description

  1   tests/FakeProgram/HelloWorldFile2.php:11
      Tests\FakeProgram\HelloWorldFile3::say()

  2   tests/FakeProgram/HelloWorldFile1.php:11
      Tests\FakeProgram\HelloWorldFile2::say()
EOF;

        $this->assertStringContainsString(
            $result,
            $writer->getOutput()
                ->fetch()
        );
    }

    /** @test */
    public function itHidesTrace(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->showTrace(false)
            ->write($inspector);

        $result = <<<EOF

   Tests\FakeProgram\FakeException 

  Fail description

  at tests/FakeProgram/HelloWorldFile3.php:11
      7▕ class HelloWorldFile3
      8▕ {
      9▕     public static function say()
     10▕     {
  ➜  11▕         return new FakeException('Fail description');
     12▕     }
     13▕ }
     14▕

EOF;

        $this->assertStringContainsString(
            $result,
            $writer->getOutput()
                ->fetch()
        );
    }

    /** @test */
    public function itSupportsRenderlessContracts(): void
    {
        $inspector = new Inspector(HelloWorldFile4::say());

        ($writer = $this->createWriter())->write($inspector);

        $result = <<<EOF

   Tests\FakeProgram\FakeRenderlessException \n
  Fail renderless description\n
EOF;

        $this->assertEquals(
            $writer->getOutput()
                ->fetch(),
            $result
        );
    }

    protected function createWriter()
    {
        $output = new BufferedOutput();

        $colorMock = $this->createPartialMock(ConsoleColor::class, ['isSupported']);

        return new Writer(new NullSolutionsRepository(), $output, null, new Highlighter($colorMock));
    }
}
