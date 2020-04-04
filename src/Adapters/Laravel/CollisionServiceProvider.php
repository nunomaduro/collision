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

use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\Commands\TestCommand;
use NunoMaduro\Collision\Contracts\Provider as ProviderContract;
use NunoMaduro\Collision\Handler;
use NunoMaduro\Collision\Provider;
use NunoMaduro\Collision\SolutionsRepositories\NullSolutionsRepository;
use NunoMaduro\Collision\Writer;

/**
 * This is an Collision Laravel Adapter Service Provider implementation.
 *
 * Registers the Error Handler on Laravel.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class CollisionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     *
     * @var bool
     */
    protected $defer = true;

    /**
     * Boots application services.
     *
     * @return void
     */
    public function boot()
    {
        $this->commands([
            TestCommand::class,
        ]);
    }

    /**
     * {@inheritdoc}
     */
    public function register()
    {
        if ($this->app->runningInConsole() && !$this->app->runningUnitTests()) {
            $this->app->bind(ProviderContract::class, function () {
                if ($this->app->has(\Facade\IgnitionContracts\SolutionProviderRepository::class)) {
                    $solutionsRepository = new IgnitionSolutionsRepository(
                        $this->app->get(\Facade\IgnitionContracts\SolutionProviderRepository::class)
                    );
                } else {
                    $solutionsRepository = new NullSolutionsRepository();
                }

                $writer = new Writer($solutionsRepository);
                $handler = new Handler($writer);

                return new Provider(null, $handler);
            });

            $appExceptionHandler = $this->app->make(ExceptionHandlerContract::class);

            $this->app->singleton(
                ExceptionHandlerContract::class,
                function ($app) use ($appExceptionHandler) {
                    return new ExceptionHandler($app, $appExceptionHandler);
                }
            );
        }
    }

    /**
     * {@inheritdoc}
     */
    public function provides()
    {
        return [ProviderContract::class];
    }
}
