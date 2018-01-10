<?php

namespace Tests\Unit\Adapters;

use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use Whoops\Exception\Inspector;
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
        $listenerMock->addError(new FakeTest, new FakeException(), 0);
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
            new FakeTest, $exception, 0
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
