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
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Foundation\Exceptions\Handler as LaravelHandler;
use NunoMaduro\Collision\Contracts\Adapters\Phpunit\Listener as ListenerContract;

/**
 * This is an Collision Laravel Adapter ExceptionHandler implementation.
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
    public function render($request, Exception $e)
    {
        if (app('env') === 'testing' && ! $e instanceof HttpException) {
            app(ListenerContract::class)->render($e);
        }

        return parent::render($request, $e);
    }

    /**
     * {@inheritdoc}
     */
    public function renderForConsole($output, Exception $e)
    {
        $handler = (new Provider)->register()
            ->getHandler()
            ->setOutput($output);

        $handler->setInspector((new Inspector($e)));

        $handler->handle();
    }
}
