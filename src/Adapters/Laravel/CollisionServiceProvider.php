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

use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Contracts\Handler;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

/**
 * This is an Collision Laravel Service Provider implementation.
 *
 * Registers the Error Handler on Laravel.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class CollisionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        if ($this->app->runningInConsole()) {
            $this->app->singleton(ExceptionHandlerContract::class, ExceptionHandler::class);
        }
    }
}
