<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class PhpunitTest extends TestCase
{
    #[Test]
    public function it_is_a_printer(): void
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
    public function it_has_tests(): void
    {
        $output = $this->runCollisionTests([
            '--exclude-group=fail',
            '--exclude-group=environmentTesting',
            '--exclude-group=environmentCustomVariables',
            '--exclude-group=custom-name',
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
    public function it_has_custom_test_case_name(): void
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
    public function it_printed_unexpected_output(): void
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
    public function it_has_a_todo(): void
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
    public function it_has_recap(): void
    {
        $output = $this->runCollisionTests([
            '--exclude-group=fail',
            '--exclude-group=environmentTesting',
            '--exclude-group=environmentCustomVariables',
        ]);

        $this->assertConsoleOutputContainsString(
            'Tests:    2 deprecated, 2 warnings, 1 risky, 1 incomplete, 2 notices, 1 todo, 1 skipped, 9 passed (16 assertions)',
            $output
        );

        $this->assertConsoleOutputNotContainsString(
            'Random Order Seed:',
            $output
        );
    }

    #[Test]
    public function it_has_recap_with_random_order_seed(): void
    {
        $output = $this->runCollisionTests([
            '--order-by=random',
            '--random-order-seed=123',
            '--exclude-group=fail',
            '--exclude-group=environmentTesting',
            '--exclude-group=environmentCustomVariables',
        ]);

        $this->assertConsoleOutputContainsString(
            'Random Order Seed: 123',
            $output
        );
    }

    #[Test]
    public function it_informs_the_user_when_no_tests_are_executed(): void
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
    public function it_has_failure(): void
    {
        $output = $this->runCollisionTests([], 1);

        $code = '$this->assertFalse(true);';
        $space = ' ';

        $this->assertConsoleOutputContainsString(<<<EOF
  Failed asserting that true is false.

  at tests/LaravelApp/tests/Unit/ExampleTest.php:15
     11▕ {
     12▕     #[Group('fail')]
     13▕     public function test_fail_example()
     14▕     {
  ➜  15▕         $code
     16▕     }
     17▕$space
     18▕     #[Group('todo')]
     19▕     public function test_todo_example()

  1   tests/LaravelApp/tests/Unit/ExampleTest.php:15

EOF
            , $output);
    }

    private function runCollisionTests(array $arguments = [], int $exitCode = 0): string
    {
        if (! file_exists('./vendor/bin/pest')) {
            $this->markTestSkipped('Pest is not installed.');
        }

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
    public function it_has_output_in_stdout_with_be_strict_about_output_during_tests_false(): void
    {
        if (! file_exists('./vendor/bin/pest')) {
            $this->markTestSkipped('Pest is not installed.');
        }

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

        $output = $process->getOutput();

        try {
            $this->assertConsoleOutputContainsString(<<<OUTPUT

               WARN  TestCaseWithStdoutOutput\OutputTest
              ! with output → This test printed output: Foo
              ✓ nothing special
              ✓ with no output

              Tests:    1 risky, 2 passed (3 assertions)
            OUTPUT, $output);
        } catch (ExpectationFailedException) {
            $this->assertConsoleOutputContainsString(<<<OUTPUT

               WARN  TestCaseWithStdoutOutput\OutputTest
              ! with output → Test code or tested code printed unexpected output: Foo
              ✓ nothing special
              ✓ with no output

              Tests:    1 risky, 2 passed (3 assertions)
            OUTPUT, $output);
        }

        $this->assertConsoleOutputNotContainsString(
            'Bar',
            $process->getOutput()
        );
    }
}
