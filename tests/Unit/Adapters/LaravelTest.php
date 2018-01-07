<?php

namespace Tests\Unit\Adapters;

use Exception;
use Illuminate\Container\Container;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;
use PHPUnit\Framework\TestCase;
use Illuminate\Foundation\Application;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;

class LaravelTest extends TestCase
{
    /** @test */
    public function it_is_registered_on_artisan(): void
    {
        $app = $this->createApplication();
        $app->method('runningInConsole')->willReturn(true);
        $app->method('runningUnitTests')->willReturn(false);

        (new CollisionServiceProvider($app))->register();

        $this->assertInstanceOf(ExceptionHandler::class, $app->make(ExceptionHandlerContract::class));
    }

    /** @test */
    public function it_is_not_registered_on_testing(): void
    {
        $app = $this->createApplication();
        $app->method('runningInConsole')->willReturn(true);
        $app->method('runningUnitTests')->willReturn(true);

        (new CollisionServiceProvider($app))->register();

        $this->assertNotInstanceOf(ExceptionHandler::class, $app->make(ExceptionHandlerContract::class));
    }

    /** @test */
    public function it_is_not_registered_on_http(): void
    {
        $app = $this->createApplication();
        $app->method('runningInConsole')->willReturn(false);
        $app->method('runningUnitTests')->willReturn(false);

        (new CollisionServiceProvider($app))->register();

        $this->assertNotInstanceOf(ExceptionHandler::class, $app->make(ExceptionHandlerContract::class));
    }

    /** @test */
    public function exception_handler_respects_is_contract(): void
    {
        $app = $this->createApplication();

        $this->assertInstanceOf(
            ExceptionHandlerContract::class,
            new ExceptionHandler($app, $app->make(ExceptionHandlerContract::class))
        );
    }

    /** @test */
    public function it_reports_to_the_original_exception_handler(): void
    {
        $app = $this->createApplication();
        $exception = new Exception();
        $originalExceptionHandlerMock = $this->createMock(ExceptionHandlerContract::class);
        $originalExceptionHandlerMock->expects($this->once())->method('report')->with($exception);

        $exceptionHandler = new ExceptionHandler($app, $originalExceptionHandlerMock);
        $exceptionHandler->report($exception);
    }

    /** @test */
    public function it_renders_non_symfony_console_exceptions(): void
    {
        $app = $this->createApplication();
        $exception = new Exception();
        $originalExceptionHandlerMock = $this->createMock(ExceptionHandlerContract::class);
        $originalExceptionHandlerMock->expects($this->once())->method('report')->with($exception);

        $exceptionHandler = new ExceptionHandler($app, $originalExceptionHandlerMock);
        $exceptionHandler->report($exception);
    }

    /**
     * Creates a new instance of Laravel Application.
     *
     * @return \PHPUnit\Framework\MockObject\MockObject
     */
    private function createApplication()
    {
        $app = $this->createPartialMock(Application::class, ['runningInConsole', 'runningUnitTests']);

        Container::setInstance($app);

        $app->singleton(
            ExceptionHandlerContract::class,
            function () use ($app) {
                return new \Illuminate\Foundation\Exceptions\Handler($app);
            }
        );

        return $app;
    }
}
