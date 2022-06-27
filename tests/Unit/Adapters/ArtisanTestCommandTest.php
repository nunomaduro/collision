<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Process\Process;

class ArtisanTestCommandTest extends TestCase
{
    /** @test */
    public function testCoverage(): void
    {
        $output = $this->runTests(['./tests/LaravelApp/artisan', 'test', '--coverage', '--group', 'coverage']);
        $this->assertStringContainsString('Console/Kernel', $output);
        $this->assertStringContainsString('0.0', $output);
        $this->assertStringContainsString('Total Coverage', $output);

        $output = $this->runTests(['./tests/LaravelApp/artisan', 'test', '--coverage', '--parallel', '--group', 'coverage']);
        $this->assertStringContainsString('Console/Kernel', $output);
        $this->assertStringContainsString('0.0', $output);
        $this->assertStringContainsString('Total Coverage', $output);
    }

    /** @test */
    public function testMinCoverage(): void
    {
        $output = $this->runTests(['./tests/LaravelApp/artisan', 'test', '--coverage', '--min=10', '--group', 'coverage']);
        $this->assertStringContainsString('Total Coverage', $output);
        $this->assertStringNotContainsString('Code coverage below expected', $output);

        $output = $this->runTests(['./tests/LaravelApp/artisan', 'test', '--coverage', '--min=10', '--parallel', '--group', 'coverage']);
        $this->assertStringContainsString('Total Coverage', $output);
        $this->assertStringNotContainsString('Code coverage below expected', $output);

        $output = $this->runTests(['./tests/LaravelApp/artisan', 'test', '--coverage', '--min=99', '--group', 'coverage'], 1);
        $this->assertStringContainsString('Total Coverage', $output);
        $this->assertStringContainsString('Code coverage below expected', $output);

        $output = $this->runTests(['./tests/LaravelApp/artisan', 'test', '--coverage', '--min=99', '--parallel', '--group', 'coverage'], 1);
        $this->assertStringContainsString('Total Coverage', $output);
        $this->assertStringContainsString('Code coverage below expected', $output);
    }

    /** @test */
    public function testEnv(): void
    {
        $this->runTests([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--group',
            'environment',
        ]);

        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--group', 'environment']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--group', 'environment']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--recreate-databases', '--group', 'environment']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--drop-databases', '--group', 'environment']);
    }

    /** @test */
    public function testEnvTesting(): void
    {
        file_put_contents(__DIR__.'/../../../tests/LaravelApp/.env.testing', <<<'EOF'
VAR_IN_DOT_ENV_TESTING=VAL_IN_DOT_ENV_TESTING
VAR_OVERRIDDEN_IN_PHPUNIT=VAL_THAT_SHOULD_BE_OVERRIDDEN
EOF
        );

        $this->runTests([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--group',
            'environmentTesting',
        ]);

        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--group', 'environmentTesting']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--group', 'environmentTesting']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--recreate-databases', '--group', 'environmentTesting']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--drop-databases', '--group', 'environmentTesting']);
    }

    /**
     * @afterClass
     */
    public static function cleanUp()
    {
        @unlink(__DIR__.'/../../../tests/LaravelApp/.env.testing');
    }

    /** @test */
    public function testExtendableCustomVariables(): void
    {
        $this->runTests([
            './vendor/bin/phpunit',
            '-c',
            'tests/LaravelApp/phpunit.xml',
            '--group',
            'environmentNoCVPhpunit',
        ]);

        // Without Custom Variables (-c|--custom-argument)
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--group', 'environmentNoCVPhpunit']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--group', 'environmentNoCVParallel']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--recreate-databases', '--group', 'environmentNoCVParallelRecreate']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--parallel', '--drop-databases', '--group', 'environmentNoCVParallelDrop']);

        // With Custom Variables (-c)
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '-c', '--group', 'environmentCVPhpunit']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '-c', '--parallel', '--group', 'environmentCVParallel']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '-c', '--parallel', '--recreate-databases', '--group', 'environmentCVParallelRecreate']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '-c', '--parallel', '--drop-databases', '--group', 'environmentCVParallelDrop']);

        // With Custom Variables (--custom-argument)
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--custom-argument', '--group', 'environmentCVPhpunit']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--custom-argument', '--parallel', '--group', 'environmentCVParallel']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--custom-argument', '--parallel', '--recreate-databases', '--group', 'environmentCVParallelRecreate']);
        $this->runTests(['./tests/LaravelApp/artisan', 'test', '--custom-argument', '--parallel', '--drop-databases', '--group', 'environmentCVParallelDrop']);
    }

    private function runTests(array $arguments, int $expectedExitCode = 0): string
    {
        $process = new Process($arguments, __DIR__.'/../../..', [
            'XDEBUG_MODE' => 'coverage',
        ]);
        $process->setPty(true);
        $process->run();

        $output = $process->getOutput();

        $failedOutput = <<<EOF
--- ASSERTION FAIL RECAP ---
$output
----------------------------
EOF;

        $this->assertEquals($expectedExitCode, $process->getExitCode(), $failedOutput);

        return $output;
    }
}
