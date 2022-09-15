<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use PHPUnit\Event\Code\Test;
use PHPUnit\Event\Code\TestMethod;
use PHPUnit\Event\Telemetry\Info;

/**
 * @internal
 */
final class State
{
    /**
     * The complete test suite tests.
     *
     * @var array<string, TestResult>
     */
    public array $suiteTests = [];

    /**
     * The current test case class.
     */
    public string|null $testCaseName;

    /**
     * The current test case tests.
     *
     * @var array<string, TestResult>
     */
    public array $testCaseTests = [];

    /**
     * The current test case tests.
     *
     * @var array<string, TestResult>
     */
    public array $toBePrintedCaseTests = [];

    /**
     * Header printed.
     */
    public bool $headerPrinted = false;

    /**
     * The state constructor.
     */
    public function __construct()
    {
        $this->testCaseName = '';
    }

    /**
     * Checks if the given test already contains a result.
     */
    public function existsInTestCase(Test $test): bool
    {
        return isset($this->testCaseTests[$test->id()]);
    }

    /**
     * Adds the given test to the State.
     */
    public function add(TestResult $test): void
    {
        $this->testCaseName = $test->testCaseName;

        $this->testCaseTests[$test->id] = $test;
        $this->toBePrintedCaseTests[$test->id] = $test;

        $this->suiteTests[$test->id] = $test;
    }

    /**
     * Sets the telemetry for the given test.
     */
    public function setTelemetry(Test $test, Info $telemetry): void
    {
        $this->testCaseTests[$test->id()]->setTelemetry($telemetry);
    }

    /**
     * Gets the test case title.
     */
    public function getTestCaseTitle(): string
    {
        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::FAIL) {
                return 'FAIL';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type !== TestResult::PASS) {
                return 'WARN';
            }
        }

        return 'PASS';
    }

    /**
     * Gets the test case title color.
     */
    public function getTestCaseTitleColor(): string
    {
        foreach ($this->testCaseTests as $test) {
            if ($test->type === TestResult::FAIL) {
                return 'red';
            }
        }

        foreach ($this->testCaseTests as $test) {
            if ($test->type !== TestResult::PASS) {
                return 'yellow';
            }
        }

        return 'green';
    }

    /**
     * Returns the number of tests on the current test case.
     */
    public function testCaseTestsCount(): int
    {
        return count($this->testCaseTests);
    }

    /**
     * Returns the number of tests on the complete test suite.
     */
    public function testSuiteTestsCount(): int
    {
        return count($this->suiteTests);
    }

    /**
     * Checks if the given test case is different from the current one.
     */
    public function testCaseHasChanged(TestMethod $test): bool
    {
        return self::getPrintableTestCaseName($test) !== $this->testCaseName;
    }

    /**
     * Moves the an new test case.
     */
    public function moveTo(TestMethod $test): void
    {
        $this->testCaseName = self::getPrintableTestCaseName($test);

        $this->testCaseTests = [];

        $this->headerPrinted = false;
    }

    /**
     * Foreach test in the test case.
     */
    public function eachTestCaseTests(callable $callback): void
    {
        foreach ($this->toBePrintedCaseTests as $test) {
            $callback($test);
        }

        $this->toBePrintedCaseTests = [];
    }

    public function countTestsInTestSuiteBy(string $type): int
    {
        return count(array_filter($this->suiteTests, function (TestResult $testResult) use ($type) {
            return $testResult->type === $type;
        }));
    }

    /**
     * Returns the printable test case name from the given `TestCase`.
     */
    public static function getPrintableTestCaseName(TestMethod $test): string
    {
        $className = explode('::', $test->id())[0];

        if (is_subclass_of($className, HasPrintableTestCaseName::class)) {
            return $className::getPrintableTestCaseName();
        }

        return $className;
    }
}
