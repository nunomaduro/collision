<?php

declare(strict_types=1);

namespace App\Console\Commands;

use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand as BaseTestCommand;

class TestCommand extends BaseTestCommand
{
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
            return [PHP_BINARY, '-qrr', __DIR__ . '/../../../../../vendor/bin/phpunit'];
        }

        return [PHP_BINARY, __DIR__ . '/../../../../../vendor/bin/phpunit'];
    }
}
