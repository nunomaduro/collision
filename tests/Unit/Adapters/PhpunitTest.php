<?php

namespace Tests\Unit\Adapters;

use NunoMaduro\Collision\Adapters\Phpunit\Listener;
use NunoMaduro\Collision\Contracts\Writer;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestResult;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Whoops\Exception\Inspector;

class PhpunitTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(\NunoMaduro\Collision\Contracts\Adapters\Phpunit\Listener::class, new Listener());
    }

    /** @test */
    public function it_renders_exceptions_using_the_writer(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['terminate']);

        $writerMock = $this->createMock(Writer::class);
        $exception = new FakeException();
        $listenerMock->expects($this->once())->method('terminate');
        $writerMock->expects($this->once())->method('write')->with(new Inspector($exception));
        $test = new FakeTest();
        $listenerMock->__construct($writerMock);
        $listenerMock->render($test, $exception);
    }

    /** @test */
    public function it_adds_an_error(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $exception = new FakeException();
        $test = new FakeTest();
        $listenerMock->expects($this->once())->method('render')->with($test, $exception);
        $listenerMock->render($test, $exception);
    }

    /** @test */
    public function it_dont_adds_an_warning(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $exception = new Warning();
        $listenerMock->expects($this->never())->method('render')->with($exception);
        $listenerMock->addWarning(new FakeTest, $exception, 0);
    }

    /** @test */
    public function it_dont_renders_nothing_test_status_methods(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $exception = new FakeException();
        $listenerMock->expects($this->never())->method('render')->with($exception);
        $listenerMock->addIncompleteTest(new FakeTest, $exception, 0);
        $listenerMock->addRiskyTest(new FakeTest, $exception, 0);
        $listenerMock->addSkippedTest(new FakeTest, $exception, 0);
    }

    /** @test */
    public function it_dont_renders_nothing_on_test_suite_methods(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $testSuite = new TestSuite();
        $listenerMock->expects($this->never())->method('render');
        $listenerMock->startTestSuite($testSuite);
        $listenerMock->endTestSuite($testSuite);
        $listenerMock->startTest($testSuite);
        $listenerMock->endTest($testSuite, 0);
    }

    /** @test */
    public function it_adds_an_failure(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $exception = new FakeException();
        $test = new FakeTest();
        $listenerMock->expects($this->once())->method('render')->with($test, $exception);

        $listenerMock->addFailure(
            $test,
            $exception,
            0
        );
    }
}

class FakeTest implements Test
{
    public function run(TestResult $result = null): TestResult
    {
    }

    public function count(): int
    {
        return 0;
    }
}

class FakeException extends AssertionFailedError
{
}
