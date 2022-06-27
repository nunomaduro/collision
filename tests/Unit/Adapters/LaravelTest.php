<?php

declare(strict_types=1);

namespace Tests\Unit\Adapters;

use Exception;
use Illuminate\Container\Container;
use Illuminate\Contracts\Debug\ExceptionHandler as ExceptionHandlerContract;
use Illuminate\Foundation\Application;
use NunoMaduro\Collision\Adapters\Laravel\CollisionServiceProvider;
use NunoMaduro\Collision\Adapters\Laravel\ExceptionHandler;
use NunoMaduro\Collision\Adapters\Laravel\Inspector;
use NunoMaduro\Collision\Contracts\Handler;
use NunoMaduro\Collision\Contracts\Provider as ProviderContract;
use PHPUnit\Framework\TestCase;
use ReflectionMethod;
use Symfony\Component\Console\Exception\InvalidArgumentException;
use Symfony\Component\Console\Output\BufferedOutput;

class LaravelTest extends TestCase
{
    /** @test */
    public function itIsRegisteredOnArtisan(): void
    {
        $app = $this->createApplication();
        $app->method('runningInConsole')->willReturn(true);
        $app->method('runningUnitTests')->willReturn(false);

        (new CollisionServiceProvider($app))->register();

        $this->assertInstanceOf(ExceptionHandler::class, $app->make(ExceptionHandlerContract::class));
    }

    /** @test */
    public function itIsNotRegisteredOnTesting(): void
    {
        $app = $this->createApplication();
        $app->method('runningInConsole')->willReturn(true);
        $app->method('runningUnitTests')->willReturn(true);

        (new CollisionServiceProvider($app))->register();

        $this->assertNotInstanceOf(ExceptionHandler::class, $app->make(ExceptionHandlerContract::class));
    }

    /** @test */
    public function itIsNotRegisteredOnHttp(): void
    {
        $app = $this->createApplication();
        $app->method('runningInConsole')->willReturn(false);
        $app->method('runningUnitTests')->willReturn(false);

        (new CollisionServiceProvider($app))->register();

        $this->assertNotInstanceOf(ExceptionHandler::class, $app->make(ExceptionHandlerContract::class));
    }

    /** @test */
    public function exceptionHandlerRespectsIsContract(): void
    {
        $app = $this->createApplication();

        $this->assertInstanceOf(
            ExceptionHandlerContract::class,
            new ExceptionHandler($app, $app->make(ExceptionHandlerContract::class))
        );
    }

    /** @test */
    public function itReportsToTheOriginalExceptionHandler(): void
    {
        $app = $this->createApplication();
        $exception = new Exception();
        $originalExceptionHandlerMock = $this->createMock(ExceptionHandlerContract::class);
        $originalExceptionHandlerMock->expects($this->once())->method('report')->with($exception);

        $exceptionHandler = new ExceptionHandler($app, $originalExceptionHandlerMock);
        $exceptionHandler->report($exception);
    }

    /** @test */
    public function itRendersToTheOriginalExceptionHandler(): void
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
    public function itRendersNonSymfonyConsoleExceptionsWithCollision(): void
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
    public function itRendersNonSymfonyConsoleExceptionsWithSymfony(): void
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
    public function isInspectorGetsTrace(): void
    {
        $method = new ReflectionMethod(Inspector::class, 'getTrace');
        $method->setAccessible(true);

        $exception = new Exception('Foo');

        $this->assertSame($method->invokeArgs(new Inspector($exception), [$exception]), $exception->getTrace());
    }

    /** @test */
    public function itProvidesOnlyTheProviderContract(): void
    {
        $app = $this->createApplication();
        $provides = (new CollisionServiceProvider($app))->provides();
        $this->assertEquals([ProviderContract::class], $provides);
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
