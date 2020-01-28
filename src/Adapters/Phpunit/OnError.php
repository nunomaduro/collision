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

use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExceptionWrapper;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;
use Whoops\Exception\Inspector;

/**
 * @internal
 */
final class OnError
{
    /**
     * Displays the error using Collision's writer
     * and terminates with exit code === 1.
     *
     * @param  OutputInterface  $output
     * @param  Throwable  $throwable
     *
     * @return void
     */
    public static function display(OutputInterface $output, Throwable $throwable)
    {
        $writer = (new Writer())->setOutput($output);

        if ($throwable instanceof AssertionFailedError) {
            $writer->showTitle(false);
            $output->write('', true);
        }

        $writer->ignoreFilesIn([
            '/vendor\/phpunit\/phpunit\/src/',
            '/vendor\/mockery\/mockery/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Testing/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Testing/',
        ]);

        if ($throwable instanceof ExceptionWrapper && $throwable->getOriginalException() !== null) {
            $throwable = $throwable->getOriginalException();
        }

        $inspector = new Inspector($throwable);

        $writer->write($inspector);

        if ($throwable instanceof ExpectationFailedException && $comparisionFailure = $throwable->getComparisonFailure()) {
            $output->write($comparisionFailure->getDiff());
        }

        exit(1);
    }
}
