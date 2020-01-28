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
    public function test_successful_output(): void
    {
        $process = new Process([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--printer',
            'NunoMaduro\Collision\Adapters\Phpunit\Listener',
            '--exclude-group',
            'fail',
        ], __DIR__.'/../../..');

        $process->setTty(false);
        $process->setPty(false);
        $process->run();

        self::assertStringContainsString(<<<'EOF'

  s skipped example → This is a skip description
  i incomplete example → This is a incomplete description
  r risky example
  w warn example → This is a warning description
  ✓ pass example

EOF
            , $process->getOutput());
        $this->assertTrue($process->isSuccessful());
    }

    /** @test */
    public function test_failing_output(): void
    {
        $process = new Process([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--printer',
            'NunoMaduro\Collision\Adapters\Phpunit\Listener',
        ], __DIR__.'/../../..');

        $process->setTty(false);
        $process->setPty(false);
        $process->run();
        $output = $process->getOutput();

        $code = '$this->assertFalse(true);';

        self::assertStringContainsString(<<<EOF
  ✓ basic test
  ✕ fail example

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
            , $output);

        $this->assertFalse($process->isSuccessful());
    }
}
