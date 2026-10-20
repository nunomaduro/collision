<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\ConsoleColor;
use NunoMaduro\Collision\Highlighter;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\BufferedOutput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use Tests\FakeProgram\HelloWorldFile1;
use Tests\FakeProgram\HelloWorldFile4;
use Tests\FakeProgram\HelloWorldFile5;
use Tests\FakeProgram\vendor\HelloWorldVendorFile;
use Whoops\Exception\Frame;
use Whoops\Exception\Inspector;

class WriterTest extends TestCase
{
    #[Test]
    public function it_gets_the_output(): void
    {
        $writer = new Writer(new NullSolutionsRepository, $output = new ConsoleOutput);

        $this->assertEquals($writer->getOutput(), $output);
    }

    #[Test]
    public function it_sets_the_output(): void
    {
        $writer = (new Writer)->setOutput($output = new ConsoleOutput);

        $this->assertEquals($writer->getOutput(), $output);
    }

    #[Test]
    public function it_writes_the_exception(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->write($inspector);
        $space = ' ';

        $result = <<<EOF

   Tests\FakeProgram\FakeException$space

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

    #[Test]
    public function it_writes_details(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        $writer = $this->createWriter();
        $writer->getOutput()->setVerbosity(OutputInterface::VERBOSITY_DEBUG);

        $writer->write($inspector);
        $space = ' ';

        $result = <<<EOF

   Tests\FakeProgram\FakeException$space

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

        $this->assertStringContainsString($result, $writer->getOutput()->fetch());
    }

    #[Test]
    public function it_ignores_closures(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->ignoreFilesIn([function (Frame $frame) {
            return str_contains($frame->getFile(), 'FakeProgram');
        }])
            ->write($inspector);

        $space = ' ';

        $result = <<<EOF

   Tests\FakeProgram\FakeException$space

  Fail description

  at tests/Unit/WriterTest.php
EOF;

        $this->assertStringContainsString(
            $result,
            $writer->getOutput()->fetch()
        );
    }

    #[Test]
    public function it_ignores_folders(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->ignoreFilesIn(['*/FakeProgram/*'])
            ->write($inspector);

        $space = ' ';

        $result = <<<EOF

   Tests\FakeProgram\FakeException$space

  Fail description

  at tests/Unit/WriterTest.php
EOF;

        $this->assertStringContainsString(
            $result,
            $writer->getOutput()
                ->fetch()
        );
    }

    #[Test]
    public function it_hides_editor(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->showEditor(false)
            ->write($inspector);

        $space = ' ';

        $result = <<<EOF

   Tests\FakeProgram\FakeException$space

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

    #[Test]
    public function it_hides_trace(): void
    {
        $inspector = new Inspector(HelloWorldFile1::say());

        ($writer = $this->createWriter())->showTrace(false)
            ->write($inspector);

        $space = ' ';

        $result = <<<EOF

   Tests\FakeProgram\FakeException$space

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

    #[Test]
    public function it_supports_renderless_contracts(): void
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

    #[Test]
    public function it_supports_custom_editor_contracts(): void
    {
        $inspector = new Inspector(HelloWorldFile5::say());

        ($writer = $this->createWriter())->write($inspector);
        $space = ' ';
        $result = <<<EOF

   Tests\FakeProgram\FakeRenderableOnCollisionEditorException$space

  Fail custom editor description

  at tests/FakeProgram/FakeRenderableOnCollisionEditorException.php:16
     12▕ {
     13▕     public function __construct(private string \$collisionFile, private int \$collisionLine, string \$message)
     14▕     {
     15▕         parent::__construct(\$message);
  ➜  16▕     }
     17▕$space
     18▕     /**
     19▕      * {@inheritDoc}
     20▕      */

  1   tests/FakeProgram/HelloWorldFile5.php:11
      Tests\FakeProgram\FakeRenderableOnCollisionEditorException::("Fail custom editor description")

  2   tests/Unit/WriterTest.php:261
      Tests\FakeProgram\HelloWorldFile5::say()


EOF;

        $this->assertEquals(
            $writer->getOutput()
                ->fetch(),
            $result
        );
    }

    #[Test]
    public function it_does_not_decorate_the_output_when_the_output_is_not_decorated(): void
    {
        $inspector = new Inspector(HelloWorldVendorFile::say());

        $colorMock = $this->createPartialMock(ConsoleColor::class, ['isSupported']);
        $colorMock->method('isSupported')->willReturn(true);

        $writer = new Writer(new NullSolutionsRepository, new BufferedOutput, null, new Highlighter($colorMock));
        $writer->write($inspector);

        $output = $writer->getOutput()->fetch();

        $this->assertStringNotContainsString("\e[", $output);
        $this->assertStringContainsString('➜  11▕', $output);
        $this->assertStringContainsString('+1 vendor frames', $output);
    }

    #[Test]
    public function it_decorates_the_output_when_the_output_is_decorated(): void
    {
        $inspector = new Inspector(HelloWorldVendorFile::say());

        $colorMock = $this->createPartialMock(ConsoleColor::class, ['isSupported']);
        $colorMock->method('isSupported')->willReturn(false);

        $writer = new Writer(new NullSolutionsRepository, new BufferedOutput(decorated: true), null, new Highlighter($colorMock));
        $writer->write($inspector);

        $output = $writer->getOutput()->fetch();

        $this->assertStringContainsString("      \e[2m+1 vendor frames \e[22m", $output);
        $this->assertStringContainsString("\e[31;1m  ➜ \e[0m", $output);
    }

    protected function createWriter()
    {
        $output = new BufferedOutput;

        $colorMock = $this->createPartialMock(ConsoleColor::class, ['isSupported']);

        return new Writer(new NullSolutionsRepository, $output, null, new Highlighter($colorMock));
    }
}
