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
use Illuminate\Contracts\Foundation\Application;
use Symfony\Component\HttpKernel\Exception\HttpException;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use NunoMaduro\Collision\Contracts\Adapters\Phpunit\Listener as ListenerContract;

/**
 * This is an Collision Laravel Adapter ExceptionHandler implementation.
 *
 * Registers the Error Handler on Laravel.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class ExceptionHandler implements ExceptionHandlerContract
{
    /**
     * Holds an instance of the application exception handler.
     *
     * @var \Illuminate\Contracts\Debug\ExceptionHandler
     */
    protected $appExceptionHandler;

    /**
     * Holds an instance of the application.
     *
     * @var \Illuminate\Contracts\Foundation\Application
     */
    protected $app;

    /**
     * Creates a new instance of the ExceptionHandler.
     *
     * @param \Illuminate\Contracts\Foundation\Application $app
     * @param \Illuminate\Contracts\Debug\ExceptionHandler $appExceptionHandler
     */
    public function __construct(Application $app, ExceptionHandlerContract $appExceptionHandler)
    {
        $this->app = $app;
        $this->appExceptionHandler = $appExceptionHandler;
    }

    /**
     * {@inheritdoc}
     */
    public function report(Exception $e)
    {
        $this->appExceptionHandler->report($e);
    }

    /**
     * {@inheritdoc}
     */
    public function render($request, Exception $e)
    {
        if ($this->app->environment() === 'testing' && ! $e instanceof HttpException) {
            $this->app->make(ListenerContract::class)
                ->render($e);
        }

        return $this->appExceptionHandler->render($request, $e);
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
