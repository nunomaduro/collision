<?php

declare(strict_types=1);

namespace App\Console\Commands;

use Illuminate\Support\Str;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as BaseTestCommand;

class TestCommand extends BaseTestCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'test
    {--without-tty : Disable output to TTY}
    {--p|parallel : Indicates if the tests should run in parallel}
    {--recreate-databases : Indicates if the test databases should be re-created}
    {--c|custom-argument : Add custom env variables}
';

    /**
     * Get the PHP binary to execute.
     *
     * @return array
     */
    protected function binary()
    {
        switch (true) {
            case $this->option('parallel'):
                $command = 'vendor/brianium/paratest/bin/paratest';
                break;
            case class_exists(\Pest\Laravel\PestServiceProvider::class):
                $command = 'vendor/pestphp/pest/bin/pest';
                break;
            default:
                $command = 'vendor/phpunit/phpunit/phpunit';
                break;
        }

        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', __DIR__ . '/../../../../../' . $command];
        }

        return [PHP_BINARY, __DIR__ . '/../../../../../' . $command];
    }

    /**
     * Get the array of environment variables for running PHPUnit.
     *
     * @return array
     */
    protected function phpunitEnvironmentVariables()
    {
        if ($this->option("custom-argument")) {
            return array_merge(
                parent::phpunitEnvironmentVariables(),
                [
                    'CUSTOM_ENV_VARIABLE'                => 1,
                    'CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'    => 1,
                ],
            );
        }

        return parent::phpunitEnvironmentVariables();
    }

    /**
     * Get the array of environment variables for running Paratest.
     *
     * @return array
     */
    protected function paratestEnvironmentVariables()
    {
        if ($this->option("custom-argument")) {
            return array_merge(
                parent::paratestEnvironmentVariables(),
                [
                    'CUSTOM_ENV_VARIABLE'                => 1,
                    'CUSTOM_ENV_VARIABLE_FOR_PARALLEL'   => 1,
                ],
            );
        }

        return parent::paratestEnvironmentVariables();
    }

    /**
     * Get the array of arguments for running PHPUnit.
     *
     * @param array $options
     *
     * @return array
     */
    protected function phpunitArguments($options)
    {
        return parent::phpunitArguments($this->filterCustomOption($options));
    }

    /**
     * Get the array of arguments for running Paratest.
     *
     * @param array $options
     *
     * @return array
     */
    protected function paratestArguments($options)
    {
        return parent::paratestArguments($this->filterCustomOption($options));
    }

    /**
     * Filters my custom argument from options list.
     *
     * @param array $options
     *
     * @return array
     */
    protected function filterCustomOption($options)
    {
        return array_values(array_filter($options, function ($option) {
            return !Str::startsWith($option, '-c')
                && !Str::startsWith($option, '--custom-argument');
        }));
    }
}
