<?php

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Phpunit\Listener;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestListener;
use PHPUnit\Framework\TestResult;
use PHPUnit\Util\Printer;
use Symfony\Component\Process\Process;

class PhpunitTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(TestListener::class, new Listener());
    }

    /** @test */
    public function it_is_a_printer(): void
    {
        $this->assertInstanceOf(Printer::class, new Listener());
    }

    /** @test */
    public function it_do_not_handles_test_that_are_not_test_cases(): void
    {
        $test = new class implements Test {
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

        (new Listener())->startTest($test);
    }

    /** @test */
    public function it_was_recap(): void
    {
        $process = $this->runTests([
            '--exclude-group',
            'fail',
        ]);

        self::assertStringContainsString(<<<EOF
  Tests:  1 warnings, 1 risky, 1 incomplete, 1 skipped, 2 passed, 6 total
EOF
            , $process->getOutput());
        $this->assertTrue($process->isSuccessful());
    }

    /** @test */
    public function it_was_failure(): void
    {
        $process = $this->runTests();

        $code = '$this->assertFalse(true);';

        self::assertStringContainsString(<<<EOF
  Failed asserting that true is false.

  at tests/LaravelApp/tests/Unit/ExampleTest.php:19
    15|      * @group fail
    16|      */
    17|     public function testFailExample()
    18|     {
  > 19|         $code
    20|     }
    21| }
    22|
EOF
            , $process->getOutput());

        $this->assertFalse($process->isSuccessful());
    }

    /**
     * @param  array  $arguments
     *
     * @return Process
     */
    private function runTests(array $arguments = []): Process
    {
        $process = new Process(array_merge([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--printer',
            'NunoMaduro\Collision\Adapters\Phpunit\Listener',
        ], $arguments), __DIR__ . '/../../..');

        $process->setTty(false);
        $process->setPty(false);
        $process->run();

        return $process;
    }
}
