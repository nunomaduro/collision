<?php

namespace Tests\Unit\Adapters;

use Exception;
use ReflectionMethod;
use PHPUnit\Framework\TestCase;
use Illuminate\Container\Container;
use Illuminate\Foundation\Application;
use NunoMaduro\Collision\Contracts\Handler;
use NunoMaduro\Collision\Adapters\Laravel\Inspector;
use Symfony\Component\Console\Output\BufferedOutput;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler;
use NunoMaduro\Collision\Contracts\Provider as ProviderContract;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;
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
    public function it_renders_to_the_original_exception_handler(): void
    {
        $app = $this->createApplication();
        $exception = new Exception();
        $request = new \stdClass();
        $originalExceptionHandlerMock = $this->createMock(ExceptionHandlerContract::class);
        $originalExceptionHandlerMock->expects($this->once())->method('render')->with($request, $exception);

        $exceptionHandler = new ExceptionHandler($app, $originalExceptionHandlerMock);
        $exceptionHandler->render($request, $exception);
    }

    /** @test */
    public function it_renders_non_symfony_console_exceptions_with_collision(): void
    {
        $app = $this->createApplication();
        $exception = new Exception();
        $output = new BufferedOutput();

        $handlerMock = $this->createMock(Handler::class);
        $handlerMock->expects($this->once())->method('setOutput')->with($output);

        $providerMock = $this->createMock(ProviderContract::class);
        $providerMock->expects($this->once())->method('register')->willReturn($providerMock);
        $providerMock->expects($this->once())->method('getHandler')->willReturn($handlerMock);
        $app->instance(ProviderContract::class, $providerMock);

        $exceptionHandler = new ExceptionHandler($app, $app->make(ExceptionHandlerContract::class));
        $exceptionHandler->renderForConsole($output, $exception);
    }

    /** @test */
    public function it_renders_non_symfony_console_exceptions_with_symfony(): void
    {
        $app = $this->createApplication();
        $exception = new InvalidArgumentException();
        $output = new BufferedOutput();

        $originalExceptionHandlerMock = $this->createMock(ExceptionHandlerContract::class);
        $originalExceptionHandlerMock->expects($this->once())->method('renderForConsole')->with($output, $exception);

        $exceptionHandler = new ExceptionHandler($app, $originalExceptionHandlerMock);
        $exceptionHandler->renderForConsole($output, $exception);
    }

    /** @test */
    public function is_inspector_gets_trace(): void
    {
        $method = new ReflectionMethod(Inspector::class, 'getTrace');
        $method->setAccessible(true);

        $exception = new Exception('Foo');

        $this->assertSame($method->invokeArgs(new Inspector($exception), [$exception]), $exception->getTrace());
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
