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
        if ('phpdbg' === PHP_SAPI) {
            return [PHP_BINARY, '-qrr', __DIR__ . '/../../../../../vendor/bin/phpunit'];
        }

        return [PHP_BINARY, __DIR__ . '/../../../../../vendor/bin/phpunit'];
    }
}
