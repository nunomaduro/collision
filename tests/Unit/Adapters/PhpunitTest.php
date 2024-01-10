<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PhpunitTest extends TestCase
{
    #[Test]
    public function itIsAPrinter(): void
    {
        $this->assertInstanceOf(DefaultPrinter::class, new DefaultPrinter(true));
    }

    private function stripConsoleOutput(string $consoleOutput)
    {
        return preg_replace('#\\x1b[[][^A-Za-z]*[A-Za-z]#', '', $consoleOutput);
    }

    private function assertConsoleOutputContainsString(string $needle, string $consoleOutput): void
    {
        self::assertStringContainsString($needle, $this->stripConsoleOutput($consoleOutput));
    }

    private function assertConsoleOutputNotContainsString(string $needle, string $consoleOutput): void
    {
        self::assertStringNotContainsString($needle, $this->stripConsoleOutput($consoleOutput));
    }

    #[Test]
    public function itHasTests(): void
    {
        $output = $this->runCollisionTests([
            '--exclude-group',
            'fail,environmentTesting,environmentCustomVariables,custom-name',
        ]);

        $this->assertConsoleOutputContainsString(<<<EOF
   WARN  Tests\Feature\ExampleTest
  - skipped example → This is a skip description
  … incomplete example → This is a incomplete description
  ! risky example → This test did not perform any assertions
  ✓ deprecation example
  ✓ pass example
This is an unexpected output
   PASS  LaravelApp\\tests\Feature\ExampleWithUnexpectedOutputTest
  ✓ pass example

  Tests:    2 deprecated, 2 warnings, 1 risky, 1 incomplete, 2 notices, 1 todo, 1 skipped, 8 passed (15 assertions)
  Duration:
EOF,
            $output
        );
    }

    #[Test]
    public function itHasCustomTestCaseName(): void
    {
        $output = $this->runCollisionTests([
            '--group',
            'custom-name',
        ]);

        $this->assertConsoleOutputContainsString(<<<'EOF'
   PASS  my-custom-test-case-name
  ✓ my-custom-test-case-name

  Tests:    1 passed (1 assertions)
  Duration:
EOF,
            $output
        );
    }

    #[Test]
    public function itPrintedUnexpectedOutput(): void
    {
        $output = $this->runCollisionTests([
            '--group',
            'unexpected-output',
        ]);

        $this->assertConsoleOutputContainsString(<<<'EOF'
This is an unexpected output
   PASS  LaravelApp\tests\Feature\ExampleWithUnexpectedOutputTest
  ✓ pass example

  Tests:    1 passed (1 assertions)
  Duration:
EOF,
            $output
        );
    }

    #[Test]
    public function itHasATodo(): void
    {
        $output = $this->runCollisionTests([
            '--group',
            'todo',
        ]);

        $this->assertConsoleOutputContainsString(<<<'EOF'
   TODO  Tests\Unit\ExampleTest - 1 todo
  ↓ todo example

  Tests:    1 todo (0 assertions)
  Duration:
EOF,
            $output
        );
    }

    #[Test]
    public function itHasRecap(): void
    {
        $output = $this->runCollisionTests([
            '--exclude-group',
            'fail,environmentTesting,environmentCustomVariables',
        ]);

        $this->assertConsoleOutputContainsString(
            'Tests:    2 deprecated, 2 warnings, 1 risky, 1 incomplete, 2 notices, 1 todo, 1 skipped, 9 passed (16 assertions)',
            $output
        );
    }

    #[Test]
    public function itInformsTheUserWhenNoTestsAreExecuted(): void
    {
        $output = $this->runCollisionTests([
            '--filter',
            'non_existing_test',
        ]);

        $this->assertConsoleOutputContainsString(
            'No tests found.',
            $output
        );
    }

    #[Test]
    public function itHasFailure(): void
    {
        $output = $this->runCollisionTests([], 1);

        $code = '$this->assertFalse(true);';
        $space = ' ';

        $this->assertConsoleOutputContainsString(<<<EOF
  Failed asserting that true is false.

  at tests/LaravelApp/tests/Unit/ExampleTest.php:15
     11▕ {
     12▕     #[Group('fail')]
     13▕     public function testFailExample()
     14▕     {
  ➜  15▕         $code
     16▕     }
     17▕$space
     18▕     #[Group('todo')]
     19▕     public function testTodoExample()

  1   tests/LaravelApp/tests/Unit/ExampleTest.php:15

EOF
            , $output);
    }

    private function runCollisionTests(array $arguments = [], int $exitCode = 0): string
    {
        $process = new Process(array_merge([
            './vendor/pestphp/pest/bin/pest',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--colors=never',
        ], $arguments), __DIR__.'/../../..', [
            'COLLISION_PRINTER' => 'DefaultPrinter',
            'COLLISION_IGNORE_DURATION' => 'true',
        ]);

        $process->run();
        $output = $process->getOutput();

        $output = str_replace(["\r\n", "\r"], "\n", $output);

        $failedOutput = <<<EOF
--- ASSERTION FAIL RECAP ---
$output
----------------------------
EOF;

        $this->assertEquals($exitCode, $process->getExitCode(), $failedOutput);

        return $process->getOutput();
    }

    #[Test]
    public function itHasOutputInStdoutWithBeStrictAboutOutputDuringTestsFalse(): void
    {
        $process = new Process([
            './vendor/bin/pest',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            'tests/TestCaseWithStdoutOutput',
            '--disallow-test-output',
        ], __DIR__.'/../../..', [
            'COLLISION_PRINTER' => 'DefaultPrinter',
            'COLLISION_IGNORE_DURATION' => 'true',
        ]);

        $process->run();

        $basePath = getcwd();

        $this->assertConsoleOutputContainsString(<<<OUTPUT

   WARN  TestCaseWithStdoutOutput\OutputTest
  ! with output → This test printed output: Foo
  ✓ nothing special
  ✓ with no output

  Tests:    1 risky, 2 passed (3 assertions)
OUTPUT
            , $process->getOutput()
        );

        $this->assertConsoleOutputNotContainsString(
            'Bar',
            $process->getOutput()
        );
    }
}
