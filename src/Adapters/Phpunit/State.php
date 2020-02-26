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
    public $testCaseClass;

    /**
     * The current test case tests.
     *
     * @var array<int, TestResult>
     */
    public $testCaseTests = [];

    /**
     * The state constructor.
     *
     * @param  string  $testCaseClass
     */
    private function __construct(string $testCaseClass)
    {
        $this->testCaseClass = $testCaseClass;
    }

    /**
     * Creates a new State starting from the given test case.
     *
     * @param  TestCase  $test
     *
     * @return self
     */
    public static function from(TestCase $test): self
    {
        return new self(get_class($test));
    }

    /**
     * Adds the given test to the State.
     *
     * @param  TestResult  $test
     *
     * @return void
     */
    public function add(TestResult $test): void
    {
        $this->testCaseTests[] = $test;

        $this->suiteTests[] = $test;
    }

    /**
     * Gets the test case title.
     *
     * @return string
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
     *
     * @return string
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
     *
     * @return int
     */
    public function testCaseTestsCount(): int
    {
        return count($this->testCaseTests);
    }

    /**
     * Returns the number of tests on the complete test suite.
     *
     * @return int
     */
    public function testSuiteTestsCount(): int
    {
        return count($this->suiteTests);
    }

    /**
     * Checks if the given test case is different from the current one.
     *
     * @param  TestCase  $testCase
     *
     * @return bool
     */
    public function testCaseHasChanged(TestCase $testCase): bool
    {
        return get_class($testCase) !== $this->testCaseClass;
    }

    /**
     * Moves the a new test case.
     *
     * @param  TestCase  $testCase
     *
     * @return void
     */
    public function moveTo(TestCase $testCase): void
    {
        $this->testCaseClass = get_class($testCase);

        $this->testCaseTests = [];
    }

    /**
     * Foreach test in the test case.
     *
     * @param  callable  $callback
     *
     * @return void
     */
    public function eachTestCaseTests(callable $callback): void
    {
        foreach ($this->testCaseTests as $test) {
            $callback($test);
        }
    }

    /**
     * @param  string  $type
     *
     * @return int
     */
    public function countTestsInTestSuiteBy(string $type): int
    {
        return count(array_filter($this->suiteTests, function (TestResult $testResult) use ($type) {
            return $testResult->type === $type;
        }));
    }

    /**
     * Checks if the given test already contains a result.
     *
     * @param  TestCase  $test
     *
     * @return bool
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
}
