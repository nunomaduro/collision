<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Laravel;

use Exception;
use NunoMaduro\Collision\Provider;
use NunoMaduro\Collision\Contracts\Handler;
use Illuminate\Foundation\Exceptions\Handler as LaravelHandler;

/**
 * This is an Collision Laravel ExceptionHandler implementation.
 *
 * Registers the Error Handler on Laravel.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class ExceptionHandler extends LaravelHandler
{
    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, Exception $e)
    {
        (new Provider)->register()
            ->getHandler()
            ->setOutput($output);

        throw $e;
    }
}
