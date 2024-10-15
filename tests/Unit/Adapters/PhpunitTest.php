<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PhpunitTest extends TestCase
{
    public function setUp(): void
    {
        if (PHP_VERSION_ID >= 80400) {
            $this->markTestSkipped('Skipped on PHP 8.4');
        }
    }

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
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

    /** @test */
    public function itHasFailure(): void
    {
        $output = $this->runCollisionTests([], 1);

        $code = '$this->assertFalse(true);';
        $space = ' ';

        $this->assertConsoleOutputContainsString(<<<EOF
  Failed asserting that true is false.

  at tests/LaravelApp/tests/Unit/ExampleTest.php:16
     12▕      * @group fail
     13▕      */
     14▕     public function testFailExample()
     15▕     {
  ➜  16▕         $code
     17▕     }
     18▕$space
     19▕     /**
     20▕      * @group todo

  1   tests/LaravelApp/tests/Unit/ExampleTest.php:16

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

    /** @test */
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
