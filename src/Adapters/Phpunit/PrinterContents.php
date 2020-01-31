<?php

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\Test;
use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use ReflectionObject;
use Symfony\Component\Console\Input\ArgvInput;
use Symfony\Component\Console\Output\ConsoleOutput;
use Throwable;

trait PrinterContents
{
    /**
     * Holds an instance of the style.
     *
     * Style is a class we use to interact with output.
     *
     * @var Style
     */
    private $style;

    /**
     * Holds the duration time of the test suite.
     *
     * @var Timer
     */
    private $timer;

    /**
     * Holds the state of the test
     * suite. The number of tests, etc.
     *
     * @var State
     */
    private $state;

    /**
     * If the test suite has ended before.
     *
     * @var bool
     */
    private $ended = false;

    /**
     * Creates a new instance of the listener.
     *
     * @param  ConsoleOutput  $output
     *
     * @throws \ReflectionException
     */
    public function __construct(ConsoleOutput $output = null)
    {
        if (intval(substr(\PHPUnit\Runner\Version::id(), 0, 1)) === 8) {
            parent::__construct();
        }

        $this->timer = Timer::start();

        $output = $output ?? new ConsoleOutput();
        ConfigureIO::of(new ArgvInput(), $output);

        $this->style = new Style($output);
        $dummyTest = new class extends TestCase {
        };

        $this->state = State::from($dummyTest);
    }

    /**
     * {@inheritdoc}
     */
    public function addError(Test $testCase, Throwable $throwable, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(TestResult::fromTestCase($testCase, TestResult::FAIL));

        $this->style->writeError($this->state, $throwable);
    }

    /**
     * {@inheritdoc}
     */
    public function addWarning(Test $testCase, Warning $warning, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(TestResult::fromTestCase($testCase, TestResult::WARN, $warning->getMessage()));
    }

    /**
     * {@inheritdoc}
     */
    public function addFailure(Test $testCase, AssertionFailedError $error, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(TestResult::fromTestCase($testCase, TestResult::FAIL));

        $reflector = new ReflectionObject($error);

        if ($reflector->hasProperty('message')) {
            $message = trim((string) preg_replace("/\r|\n/", ' ', $error->getMessage()));
            $property = $reflector->getProperty('message');
            $property->setAccessible(true);
            $property->setValue($error, $message);
        }

        $this->style->writeError($this->state, $error);
    }

    /**
     * {@inheritdoc}
     */
    public function addIncompleteTest(Test $testCase, Throwable $t, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(TestResult::fromTestCase($testCase, TestResult::INCOMPLETE));
    }

    /**
     * {@inheritdoc}
     */
    public function addRiskyTest(Test $testCase, Throwable $t, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(TestResult::fromTestCase($testCase, TestResult::RISKY, $t->getMessage()));
    }

    /**
     * {@inheritdoc}
     */
    public function addSkippedTest(Test $testCase, Throwable $t, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        $this->state->add(TestResult::fromTestCase($testCase, TestResult::SKIPPED, $t->getMessage()));
    }

    /**
     * {@inheritdoc}
     */
    public function startTestSuite(TestSuite $suite): void
    {
        if ($this->state->suiteTotalTests === null) {
            $this->state->suiteTotalTests = $suite->count();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function endTestSuite(TestSuite $suite): void
    {
        if (! $this->ended && $this->state->suiteTotalTests === $this->state->testSuiteTestsCount()) {
            $this->ended = true;

            $this->style->writeCurrentRecap($this->state);

            $this->style->updateFooter($this->state);
            $this->style->writeRecap($this->timer);
        }
    }

    /**
     * {@inheritdoc}
     */
    public function startTest(Test $testCase): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        // Let's check first if the testCase is over.
        if ($this->state->testCaseHasChanged($testCase)) {
            $this->style->writeCurrentRecap($this->state);

            $this->state->moveTo($testCase);
        }

        $this->style->updateFooter($this->state, $testCase);
    }

    /**
     * {@inheritdoc}
     */
    public function endTest(Test $testCase, float $time): void
    {
        $testCase = $this->testCaseFromTest($testCase);

        if (! $this->state->existsInTestCase($testCase)) {
            $this->state->add(TestResult::fromTestCase($testCase, TestResult::PASS));
        }
    }

    /**
     * Intentionally left blank as we output things on events of the listener.
     *
     * @param  string  $content
     *
     * @return  void
     */
    public function write(string $content): void
    {
        // ..
    }

    /**
     * Returns a test case from the given test.
     *
     * Note: This printer is do not work with normal Test classes - only
     * with Test Case classes. Please report an issue if you think
     * this should work any other way.
     *
     * @param  Test  $test
     *
     * @return TestCase
     */
    private function testCaseFromTest(Test $test): TestCase
    {
        if (! $test instanceof TestCase) {
            throw new ShouldNotHappen();
        }

        return $test;
    }
}
