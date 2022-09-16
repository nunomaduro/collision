<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Phpunit\Printer;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestResult;
use Symfony\Component\Process\Process;

class PhpunitTest extends TestCase
{
    /** @test */
    public function itRespectsIsContract(): void
    {
        $this->assertInstanceOf(TestListener::class, new Printer());
    }

    /** @test */
    public function itIsAPrinter(): void
    {
        $this->assertInstanceOf(Printer::class, new Printer());
    }

    /** @test */
    public function itDoNotHandlesTestThatAreNotTestCases(): void
    {
        $test = new class() implements Test
        {
            #[\ReturnTypeWillChange]
            public function count()
            {
                return 0;
            }

            public function run(TestResult $result = null): TestResult
            {
                // ..
            }
        };

        $this->expectException(ShouldNotHappen::class);

        (new Printer())->startTest($test);
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

        $testsDir = dirname(__DIR__, 2);

        $this->assertConsoleOutputContainsString(<<<EOF
   WARN  Tests\Feature\ExampleTest
  - skipped example → This is a skip description
  … incomplete example → This is a incomplete description
  ! risky example → This test did not perform any assertions  $testsDir/LaravelApp/tests/Feature/ExampleTest.php:21
  ! warn example → This is a warning description
  ✓ pass example

  Tests:  1 warnings, 1 risky, 1 incomplete, 1 skipped, 6 passed
  Time:
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
   PASS  my-custom-name
  ✓ testPassExample

  Tests:  1 passed
  Time:
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
            'Tests:  1 warnings, 1 risky, 1 incomplete, 1 skipped, 7 passed',
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
            'No tests executed!',
            $output
        );
    }

    /** @test */
    public function itHasFailure(): void
    {
        $output = $this->runCollisionTests([], 1);

        $code = '$this->assertFalse(true);';

        $this->assertConsoleOutputContainsString(<<<EOF
  Failed asserting that true is false.

  at tests/LaravelApp/tests/Unit/ExampleTest.php:16
     12▕      * @group fail
     13▕      */
     14▕     public function testFailExample()
     15▕     {
  ➜  16▕         $code
     17▕     }
     18▕ 
     19▕     public function testBasicTest()
     20▕     {
EOF
            , $output);
    }

    private function runCollisionTests(array $arguments = [], int $exitCode = 0): string
    {
        $process = new Process(array_merge([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--printer',
            'NunoMaduro\Collision\Adapters\Phpunit\Printer',
        ], $arguments), __DIR__.'/../../..');

        $process->run();
        $output = $process->getOutput();

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
            './vendor/bin/phpunit',
            '-c',
            'tests/TestCaseWithStdoutOutput/phpunit.xml',
            '--printer',
            'NunoMaduro\Collision\Adapters\Phpunit\Printer',
            'tests/TestCaseWithStdoutOutput',
        ], __DIR__.'/../../..');

        $process->run();

        $this->assertConsoleOutputContainsString(<<<OUTPUT
string(3) "Foo"

   PASS  TestCaseWithStdoutOutput\OutputTest
  ✓ with output
  ✓ nothing special
  ✓ with no output

  Tests:  3 passed
OUTPUT
            , $process->getOutput()
        );

        $this->assertConsoleOutputNotContainsString(
            'Bar',
            $process->getOutput()
        );
    }
}
