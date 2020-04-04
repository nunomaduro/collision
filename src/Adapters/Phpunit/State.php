<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use PHPUnit\Framework\TestCase;

/**
 * @internal
 */
final class State
{
    /**
     * The complete test suite number of tests.
     *
     * @var int|null
     */
    public $suiteTotalTests;

    /**
     * The complete test suite tests.
     *
     * @var array<int, TestResult>
     */
    public $suiteTests = [];

    /**
     * The current test case class.
     *
     * @var string
     */
    public $testCaseName;

    /**
     * The current test case tests.
     *
     * @var array<int, TestResult>
     */
    public $testCaseTests = [];

    /**
     * The state constructor.
     */
    private function __construct(string $testCaseName)
    {
        $this->testCaseName = $testCaseName;
    }

    /**
     * Creates a new State starting from the given test case.
     */
    public static function from(TestCase $test): self
    {
        return new self(self::getPrintableTestCaseName($test));
    }

    /**
     * Adds the given test to the State.
     */
    public function add(TestResult $test): void
    {
        $this->testCaseTests[] = $test;

        $this->suiteTests[] = $test;
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
    public function testCaseHasChanged(TestCase $testCase): bool
    {
        return self::getPrintableTestCaseName($testCase) !== $this->testCaseName;
    }

    /**
     * Moves the a new test case.
     */
    public function moveTo(TestCase $testCase): void
    {
        $this->testCaseName = self::getPrintableTestCaseName($testCase);

        $this->testCaseTests = [];
    }

    /**
     * Foreach test in the test case.
     */
    public function eachTestCaseTests(callable $callback): void
    {
        foreach ($this->testCaseTests as $test) {
            $callback($test);
        }
    }

    public function countTestsInTestSuiteBy(string $type): int
    {
        return count(array_filter($this->suiteTests, function (TestResult $testResult) use ($type) {
            return $testResult->type === $type;
        }));
    }

    /**
     * Checks if the given test already contains a result.
     */
    public function existsInTestCase(TestCase $test): bool
    {
        foreach ($this->testCaseTests as $testResult) {
            if (TestResult::makeDescription($test) === $testResult->description) {
                return true;
            }
        }

        return false;
    }

    /**
     * Returns the printable test case name from the given `TestCase`.
     */
    private static function getPrintableTestCaseName(TestCase $test): string
    {
        if ($test instanceof HasPrintableTestCaseName) {
            $name = $test->getPrintableTestCaseName();
        } else {
            $name = get_class($test);
        }

        return $name;
    }
}
