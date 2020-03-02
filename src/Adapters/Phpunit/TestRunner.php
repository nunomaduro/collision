<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit;

use RuntimeException;
use Symfony\Component\Console\Style\OutputStyle;
use Symfony\Component\Process\Exception\ProcessSignaledException;
use Symfony\Component\Process\Process;

class TestRunner
{
    /**
     * Test runner working path.
     *
     * @var string
     */
    protected $workingPath;

    /**
     * Disable output to TTY.
     *
     * @var bool
     */
    protected $withoutTty;

    /**
     * The arguments to be used while calling phpunit.
     *
     * @var array
     */
    protected $arguments = [
        '--printer',
        'NunoMaduro\Collision\Adapters\Phpunit\Printer',
    ];

    /**
     * Creates a new instance of the TestRunner.
     *
     * @param string $workingPath
     * @param bool $withoutTty
     */
    public function __construct(string $workingPath, bool $withoutTty = false)
    {
        $this->workingPath = $workingPath;
        $this->withoutTty = $withoutTty;
    }

    /**
     * Handle the test runner.
     *
     * @param \Symfony\Component\Console\Style\OutputStyle $output
     * @param array $options
     *
     * @return int
     */
    public function __invoke(OutputStyle $output, array $options): int
    {
        $process = (new Process(array_merge(
            $this->binary(), array_merge(
                $this->arguments,
                $this->phpunitArguments($options)
            )
        )))->setTimeout(null);

        try {
            $process->setTty(! $this->withoutTty);
        } catch (RuntimeException $e) {
            $output->writeln('Warning: '.$e->getMessage());
        }

        try {
            return $process->run(function ($type, $line) use ($output) {
                $output->write($line);
            });
        } catch (ProcessSignaledException $e) {
            if (extension_loaded('pcntl') && $e->getSignal() !== SIGINT) {
                throw $e;
            }
        }

        return 0;
    }

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary(): array
    {
        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', 'vendor/phpunit/phpunit/phpunit'];
        }

        return [PHP_BINARY, 'vendor/phpunit/phpunit/phpunit'];
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param  array  $options
     *
     * @return array
     */
    protected function phpunitArguments(array $options): array
    {
        $options = array_values(array_filter($options, function ($option) {
            return strpos($option, '--env=') !== 0;
        }));

        if (! file_exists($file = "{$this->workingPath}/phpunit.xml")) {
            $file = "{$this->workingPath}/phpunit.xml.dist";
        }

        return array_merge(['-c', $file], $options);
    }
}
