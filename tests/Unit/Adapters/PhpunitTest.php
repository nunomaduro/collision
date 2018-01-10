<?php

namespace Tests\Unit\Adapters;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\Warning;
use PHPUnit\Framework\TestCase;
use Whoops\Exception\Inspector;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\TestResult;
use NunoMaduro\Collision\Contracts\Writer;
use PHPUnit\Framework\AssertionFailedError;
use NunoMaduro\Collision\Adapters\Phpunit\Listener;

class PhpunitTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(\NunoMaduro\Collision\Contracts\Adapters\Phpunit\Listener::class, new Listener());
    }

    /** @test */
    public function it_renders_exceptions_once(): void
    {
        $writerMock = $this->createMock(Writer::class);

        $writerMock->expects($this->once())->method('write')->with($this->isInstanceOf(Inspector::class));

        $listener = new Listener($writerMock);

        $listener->render(new FakeException());
        $listener->render(new FakeException());
    }

    /** @test */
    public function it_adds_an_error(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $exception = new FakeException();
        $listenerMock->expects($this->once())->method('render')->with($exception);
        $listenerMock->addError(new FakeTest, $exception, 0);
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
        $listenerMock->endTest($testSuite, 0);
    }

    /** @test */
    public function it_stops_on_failure(): void
    {
        $testResultMock = $this->createMock(TestResult::class);
        $testResultMock->expects($this->once())->method('stopOnFailure')->with(true);

        $testMock = $this->createMock(TestCase::class);
        $testMock->expects($this->once())->method('getTestResultObject')->willReturn($testResultMock);

        (new Listener())->startTest($testMock);
    }

    /** @test */
    public function it_adds_an_failure(): void
    {
        $listenerMock = $this->createPartialMock(Listener::class, ['render']);
        $writerMock = $this->createMock(Writer::class);

        $writerMock->expects($this->once())->method('ignoreFilesIn')->willReturn($writerMock);
        $writerMock->expects($this->once())->method('showTrace')->with(false);

        $listenerMock->__construct($writerMock);

        $exception = new FakeException();
        $listenerMock->expects($this->once())->method('render')->with($exception);
        $listenerMock->addFailure(
            new FakeTest,
            $exception,
            0
        );
    }
}

class FakeTest implements Test
{
    public function run(TestResult $result = null)
    {
    }

    public function count()
    {
        return 0;
    }
}

class FakeException extends AssertionFailedError
{
}
