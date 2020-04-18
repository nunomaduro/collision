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

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Writer;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\Framework\ExceptionWrapper;
use PHPUnit\Framework\ExpectationFailedException;
use Symfony\Component\Console\Output\ConsoleOutput;
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
     * Style constructor.
     */
    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     */
    public function writeCurrentTestCaseSummary(State $state): void
    {
        if (!$state->testCaseTestsCount()) {
            return;
        }

        if ($state->headerPrinted === false) {
            $this->output->writeln($this->titleLineFrom(
                $state->getTestCaseTitle() === 'FAIL' ? 'white' : 'black',
                $state->getTestCaseTitleColor(),
                $state->getTestCaseTitle(),
                $state->testCaseName
            ));

            $state->headerPrinted = true;
        }

        $state->eachTestCaseTests(function (TestResult $testResult) {
            usleep(20000);
            $this->output->writeln($this->testLineFrom(
                $testResult->color,
                $testResult->icon,
                $testResult->description,
                $testResult->warning
            ));
        });
    }

    /**
     * Prints the content similar too:.
     *
     * ```
     *    PASS  Unit\ExampleTest
     *    ✓ basic test
     * ```
     */
    public function writeErrorsSummary(State $state): void
    {
        $errors = array_filter($state->suiteTests, function (TestResult $testResult) {
            return $testResult->type === TestResult::FAIL;
        });

        $this->output->writeln(['', '<fg=white;options=bold>  Summary of all failing tests:</>', '']);

        array_map(function (TestResult $testResult) {
            $this->output->write(sprintf(
                '  <fg=red;options=bold>• %s </>> <fg=red;options=bold>%s</>',
                $testResult->testCaseName,
                $testResult->description
            ));

            if (!$testResult->throwable instanceof Throwable) {
                throw new ShouldNotHappen();
            }

            $this->writeError($testResult->throwable);
        }, $errors);
    }

    /**
     * Writes the final recap.
     */
    public function writeRecap(State $state, Timer $timer = null): void
    {
        $types = [TestResult::FAIL, TestResult::WARN, TestResult::RISKY, TestResult::INCOMPLETE, TestResult::SKIPPED, TestResult::PASS];
        foreach ($types as $type) {
            if ($countTests = $state->countTestsInTestSuiteBy($type)) {
                $color   = TestResult::makeColor($type);
                $tests[] = "<fg=$color;options=bold>$countTests $type</>";
            }
        }

        $pending = $state->suiteTotalTests - $state->testSuiteTestsCount();
        if ($pending) {
            $tests[] = "\e[2m$pending pending\e[22m";
        }

        if (!empty($tests)) {
            $this->output->write([
                "\n",
                sprintf(
                    '  <fg=white;options=bold>Tests:  </><fg=default>%s</>',
                    implode(', ', $tests)
                ),
            ]);
        }

        if ($timer) {
            $timeElapsed = number_format($timer->result(), 2, '.', '');
            $this->output->writeln([
                    '',
                    sprintf(
                        '  <fg=white;options=bold>Time:   </><fg=default>%ss</>',
                        $timeElapsed
                    ),
                ]
            );
        }
    }

    /**
     * Displays the error using Collision's writer
     * and terminates with exit code === 1.
     */
    public function writeError(Throwable $throwable): void
    {
        $writer = (new Writer())->setOutput($this->output);

        if ($throwable instanceof AssertionFailedError) {
            $writer->showTitle(false);
            $this->output->write('', true);
        }

        $writer->ignoreFilesIn([
            '/vendor\/pestphp\/pest\/src/',
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

        $this->output->writeln('');
    }

    /**
     * Returns the title contents.
     */
    private function titleLineFrom(string $fg, string $bg, string $title, string $testCaseName): string
    {
        if (class_exists($testCaseName)) {
            $nameParts          = explode('\\', $testCaseName);
            $highlightedPart    = array_pop($nameParts);
            $nonHighlightedPart = implode('\\', $nameParts);
            $testCaseName       = sprintf("\e[2m%s\e[22m<fg=white;options=bold>%s</>", "$nonHighlightedPart\\", $highlightedPart);
        } elseif (file_exists($testCaseName)) {
            $testCaseName       = substr($testCaseName, strlen((string) getcwd()) + 1);
            $nameParts          = explode(DIRECTORY_SEPARATOR, $testCaseName);
            $highlightedPart    = (string) array_pop($nameParts);
            $highlightedPart    = substr($highlightedPart, 0, (int) strrpos($highlightedPart, '.'));
            $nonHighlightedPart = implode('\\', $nameParts);
            $testCaseName       = sprintf("\e[2m%s\e[22m<fg=white;options=bold>%s</>", "$nonHighlightedPart\\", $highlightedPart);
        }

        return sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>",
            $fg,
            $bg,
            $title,
            $testCaseName
        );
    }

    /**
     * Returns the test contents.
     */
    private function testLineFrom(string $fg, string $icon, string $description, string $warning = null): string
    {
        if (!empty($warning)) {
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
