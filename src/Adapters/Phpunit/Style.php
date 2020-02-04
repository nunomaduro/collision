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

use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExceptionWrapper;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\Framework\TestCase;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Throwable;
use Whoops\Exception\Inspector;

/**
 * @internal
 */
final class Style
{
    /**
     * @var ConsoleOutput
     */
    private $output;

    /**
     * @var ConsoleSectionOutput
     */
    private $footer;

    /**
     * Style constructor.
     *
     * @param  ConsoleOutput  $output
     */
    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;

        $this->footer = $output->section();
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     *
     * @param  State  $state
     *
     * @return void
     */
    public function writeCurrentRecap(State $state): void
    {
        if (! $state->testCaseTestsCount()) {
            return;
        }

        $this->footer->clear();

        $this->output->writeln($this->titleLineFrom(
            $state->getTestCaseTitle() === 'FAIL' ? 'white' : 'black',
            $state->getTestCaseTitleColor(),
            $state->getTestCaseTitle(),
            $state->testCaseClass
        ));

        $state->eachTestCaseTests(function (TestResult $testResult) {
            $this->output->writeln($this->testLineFrom(
                $testResult->color,
                $testResult->icon,
                $testResult->description,
                $testResult->warning
            ));
        });
    }

    /**
     * Prints the content similar too on the footer. Where
     * we are updating the current test.
     *
     * ```
     *    Runs  Unit\ExampleTest
     *    • basic test
     * ```
     *
     * @param  State  $state
     * @param  TestCase|null  $testCase
     *
     * @return void
     */
    public function updateFooter(State $state, TestCase $testCase = null): void
    {
        $runs = [];

        if ($testCase) {
            $runs[] = $this->titleLineFrom(
                'black',
                'yellow',
                'RUNS',
                get_class($testCase)
            );

            $testResult = TestResult::fromTestCase($testCase, TestResult::RUNS);
            $runs[] = $this->testLineFrom(
                $testResult->color,
                $testResult->icon,
                $testResult->description
            );
        }

        $types = [TestResult::FAIL, TestResult::WARN, TestResult::RISKY, TestResult::INCOMPLETE, TestResult::SKIPPED, TestResult::PASS];

        foreach ($types as $type) {
            if ($countTests = $state->countTestsInTestSuiteBy($type)) {
                $color = TestResult::makeColor($type);
                $tests[] = "<fg=$color;options=bold>$countTests $type</>";
            }
        }

        $pending = $state->suiteTotalTests - $state->testSuiteTestsCount();
        if ($pending) {
            $tests[] = "\e[2m$pending pending\e[22m";
        }

        if (! empty($tests)) {
            $this->footer->overwrite(array_merge($runs, [
                '',
                sprintf(
                    '  <fg=white;options=bold>Tests:  </><fg=default>%s</>',
                    implode(', ', $tests)
                ),
            ]));
        }
    }

    /**
     * Writes the final recap.
     *
     * @param  Timer  $timer
     */
    public function writeRecap(Timer $timer): void
    {
        $timeElapsed = number_format($timer->result(), 2, '.', '');
        $this->footer->writeln(
            sprintf(
                '  <fg=white;options=bold>Time:   </><fg=default>%ss</>',
                $timeElapsed
            )
        );
    }

    /**
     * Displays the error using Collision's writer
     * and terminates with exit code === 1.
     *
     * @param  Throwable  $throwable
     *
     * @return void
     */
    public function writeError(State $state, Throwable $throwable)
    {
        $this->writeCurrentRecap($state);

        $this->updateFooter($state);

        $writer = (new Writer())->setOutput($this->output);

        if ($throwable instanceof AssertionFailedError) {
            $writer->showTitle(false);
            $this->output->write('', true);
        }

        $writer->ignoreFilesIn([
            '/vendor\/phpunit\/phpunit\/src/',
            '/vendor\/mockery\/mockery/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Testing/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Testing/',
        ]);

        if ($throwable instanceof ExceptionWrapper && $throwable->getOriginalException() !== null) {
            $throwable = $throwable->getOriginalException();
        }

        $inspector = new Inspector($throwable);

        $writer->write($inspector);

        if ($throwable instanceof ExpectationFailedException && $comparisionFailure = $throwable->getComparisonFailure()) {
            $this->output->write($comparisionFailure->getDiff());
        }

        exit(1);
    }

    /**
     * Returns the title contents.
     *
     * @param  string  $fg
     * @param  string  $bg
     * @param  string  $title
     * @param  string  $testCaseClass
     *
     * @return string
     */
    private function titleLineFrom(string $fg, string $bg, string $title, string $testCaseClass): string
    {
        $classParts = explode('\\', $testCaseClass);
        // Removes `Tests` part
        array_shift($classParts);
        $highlightedPart = array_pop($classParts);
        $nonHighlightedPart = implode('\\', $classParts);

        $testCaseClass = sprintf("\e[2m%s\e[22m<fg=white;options=bold>%s</>", "$nonHighlightedPart\\", $highlightedPart);

        return sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>",
            $fg,
            $bg,
            $title,
            $testCaseClass
        );
    }

    /**
     * Returns the test contents.
     *
     * @param  string  $fg
     * @param  string  $icon
     * @param  string  $description
     *
     * @return string
     */
    private function testLineFrom(string $fg, string $icon, string $description, string $warning = null): string
    {
        if (! empty($warning)) {
            $warning = sprintf(
                ' → %s',
                $warning
            );
        }

        return sprintf(
            "  <fg=%s;options=bold>%s</><fg=default> \e[2m%s\e[22m</><fg=yellow>%s</>",
            $fg,
            $icon,
            $description,
            $warning
        );
    }
}
