<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit\Printers;

use NunoMaduro\Collision\Adapters\Phpunit\ConfigureIO;
use NunoMaduro\Collision\Adapters\Phpunit\State;
use NunoMaduro\Collision\Adapters\Phpunit\Style;
use NunoMaduro\Collision\Adapters\Phpunit\Test;
use NunoMaduro\Collision\Adapters\Phpunit\TestResult;
use NunoMaduro\Collision\Adapters\Phpunit\Timer;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Test\BeforeFirstTestMethodErrored;
use PHPUnit\Event\Test\ConsideredRisky;
use PHPUnit\Event\Test\DeprecationTriggered;
use PHPUnit\Event\Test\Errored;
use PHPUnit\Event\Test\Failed;
use PHPUnit\Event\Test\Finished;
use PHPUnit\Event\Test\MarkedIncomplete;
use PHPUnit\Event\Test\Prepared;
use PHPUnit\Event\Test\Skipped;
use PHPUnit\Event\TestRunner\ExecutionFinished;
use PHPUnit\Event\TestRunner\ExecutionStarted;
use PHPUnit\Framework\Exception;
use PHPUnit\Framework\IncompleteTestError;
use PHPUnit\Framework\SkippedWithMessageException;
use ReflectionObject;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;

/**
 * @internal
 */
final class DefaultPrinter
{
    /**
     * The output instance.
     */
    private ConsoleOutput $output;

    /**
     * The timer instance.
     */
    private Timer $timer;

    /**
     * The state instance.
     */
    private State $state;

    /**
     * The style instance.
     */
    private Style $style;

    /**
     * If the test suite has failed.
     */
    private bool $failed = false;

    /**
     * Creates a new Printer instance.
     */
    public function __construct(bool $colors)
    {
        $this->output = new ConsoleOutput(OutputInterface::VERBOSITY_NORMAL, $colors);
        $this->timer = Timer::start();

        ConfigureIO::of(new ArgvInput(), $this->output);

        $this->style = new Style($this->output);

        $this->state = new State();
    }

    /**
     * Listen to the runner execution started event.
     */
    public function testRunnerExecutionStarted(ExecutionStarted $executionStarted): void
    {
        $this->state->suiteTotalTests = $executionStarted->testSuite()->count();
    }

    /**
     * Listen to the test prepared event.
     */
    public function testPrepared(Prepared $event): void
    {
        $test = $event->test();

        if (! $test instanceof TestMethod) {
            throw new ShouldNotHappen();
        }

        if ($this->state->testCaseHasChanged($test)) {
            $this->style->writeCurrentTestCaseSummary($this->state);

            $this->state->moveTo($test);
        }
    }

    /**
     * Listen to the test errored event.
     */
    public function testBeforeFirstTestMethodErrored(BeforeFirstTestMethodErrored $event): void
    {
        $this->failed = true;

        $this->state->add(TestResult::fromBeforeFirstTestMethodErrored($event));
    }

    /**
     * Listen to the test errored event.
     */
    public function testErrored(Errored $event): void
    {
        $this->failed = true;

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $event->throwable()));
    }

    /**
     * Listen to the test failed event.
     */
    public function testFailed(Failed $event): void
    {
        $this->failed = true;

        $throwable = $event->throwable();

        $reflector = new ReflectionObject($throwable);

        if ($reflector->hasProperty('message')) {
            $message = trim((string) preg_replace("/\r|\n/", "\n  ", $throwable->asString()));

            $property = $reflector->getProperty('message');
            $property->setAccessible(true);
            $property->setValue($throwable, $message);
        }

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::FAIL, $throwable));
    }

    /**
     * Listen to the test marked incomplete event.
     */
    public function testMarkedIncomplete(MarkedIncomplete $event): void
    {
        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::INCOMPLETE, $event->throwable()));
    }

    /**
     * Listen to the test considered risky event.
     */
    public function testConsideredRisky(ConsideredRisky $event): void
    {
        $throwable = Throwable::from(new IncompleteTestError($event->message()));

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::RISKY, $throwable));
    }

    /**
     * Listen to the test deprecation triggered event.
     */
    public function testDeprecationTriggered(DeprecationTriggered $event): void
    {
        $throwable = Throwable::from(new Exception($event->message()));

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::DEPRECATED, $throwable));
    }

    /**
     * Listen to the test skipped event.
     */
    public function testSkipped(Skipped $event): void
    {
        $throwable = Throwable::from(new SkippedWithMessageException($event->message()));

        $this->state->add(TestResult::fromTestCase($event->test(), TestResult::SKIPPED, $throwable));
    }

    /**
     * Listen to the test finished event.
     */
    public function testFinished(Finished $event): void
    {
        if (! $this->state->existsInTestCase($event->test())) {
            $this->state->add(TestResult::fromTestCase($event->test(), TestResult::PASS));
        }
    }

    /**
     * Listen to the runner execution finished event.
     */
    public function testRunnerExecutionFinished(ExecutionFinished $event): void
    {
        if ($this->state->suiteTotalTests === 0) {
            $this->output->writeln([
                '',
                '  <fg=white;options=bold;bg=blue> INFO </> No tests found.',
                '',
            ]);

            return;
        }

        $this->style->writeCurrentTestCaseSummary($this->state);

        if ($this->failed) {
            $onFailure = $this->state->suiteTotalTests !== $this->state->testSuiteTestsCount();
            $this->style->writeErrorsSummary($this->state, $onFailure);
        } else {
            $this->output->write(PHP_EOL);
        }

        $this->style->writeRecap($this->state, $this->timer);
    }
}
