<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Exceptions\TestException;
use NunoMaduro\Collision\Writer;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\AssertionFailedError;
use PHPUnit\TestRunner\TestResult\Facade;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use Whoops\Exception\Inspector;

/**
 * @internal
 */
final class Style
{
    private readonly \Symfony\Component\Console\Output\ConsoleOutput $output;

    /**
     * @var string[]
     */
    private const TYPES = [TestResult::DEPRECATED, TestResult::FAIL, TestResult::WARN, TestResult::RISKY, TestResult::INCOMPLETE, TestResult::SKIPPED, TestResult::PASS];

    /**
     * Style constructor.
     */
    public function __construct(ConsoleOutputInterface $output)
    {
        if (! $output instanceof ConsoleOutput) {
            throw new ShouldNotHappen();
        }

        $this->output = $output;
    }

    /**
     * Prints the content.
     */
    public function write(string $content): void
    {
        $this->output->write($content);
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
        if ($state->testCaseTestsCount() === 0 || is_null($state->testCaseName)) {
            return;
        }

        if (! $state->headerPrinted) {
            $this->output->writeln($this->titleLineFrom(
                $state->getTestCaseTitle() === 'FAIL' ? 'default' : 'black',
                $state->getTestCaseTitleColor(),
                $state->getTestCaseTitle(),
                $state->testCaseName
            ));
            $state->headerPrinted = true;
        }

        $state->eachTestCaseTests(function (TestResult $testResult): void {
            if ($testResult->description) {
                $this->output->writeln($this->testLineFrom(
                    $testResult->color,
                    $testResult->icon,
                    $testResult->description,
                    $testResult->warning
                ));
            }
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
    public function writeErrorsSummary(State $state, bool $onFailure): void
    {
        $errors = array_filter($state->suiteTests, fn (TestResult $testResult) => $testResult->type === TestResult::FAIL);

        if (! $onFailure) {
            $this->output->writeln(['', "  \e[2m---\e[22m", '']);
        }

        array_map(function (TestResult $testResult) use ($onFailure): void {
            if (! $onFailure) {
                $this->output->write(sprintf(
                    '  <fg=red;options=bold>• %s </>> <fg=red;options=bold>%s</>',
                    $testResult->testCaseName,
                    $testResult->description
                ));
            }

            if (! $testResult->throwable instanceof Throwable) {
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
        $result = Facade::result();

        $tests = [];
        foreach (self::TYPES as $type) {
            if (($countTests = $state->countTestsInTestSuiteBy($type)) !== 0) {
                $color = TestResult::makeColor($type);
                $tests[] = "<fg=$color;options=bold>$countTests $type</>";
            }
        }

        $pending = $result->numberOfTests() - $result->numberOfTestsRun();
        if ($pending !== 0) {
            $tests[] = "\e[2m$pending pending\e[22m";
        }

        if (! empty($tests)) {
            $this->output->write([
                sprintf(
                    '  <fg=white;options=bold>Tests:  </><fg=default>%s</>',
                    implode(', ', $tests)
                ),
            ]);
        }

        if ($timer !== null) {
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

        $this->output->writeln('');
    }

    /**
     * Displays a warning message.
     */
    public function writeWarning(string $message): void
    {
        $this->output->writeln($this->testLineFrom('yellow', $message, ''));
    }

    /**
     * Displays the error using Collision's writer
     * and terminates with exit code === 1.
     */
    public function writeError(Throwable $throwable): void
    {
        $writer = (new Writer())->setOutput($this->output);

        $throwable = new TestException($throwable);

        if ($throwable->getClassName() === AssertionFailedError::class) {
            $writer->showTitle(false);

            $this->output->write('', true);
        }

        $writer->ignoreFilesIn([
            '/vendor\/bin\/pest/',
            '/bin\/pest/',
            '/vendor\/pestphp\/pest/',
            '/vendor\/phpspec\/prophecy-phpunit/',
            '/vendor\/phpspec\/prophecy/',
            '/vendor\/phpunit\/phpunit\/src/',
            '/vendor\/mockery\/mockery/',
            '/vendor\/laravel\/dusk/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Testing/',
            '/vendor\/laravel\/framework\/src\/Illuminate\/Foundation\/Testing/',
            '/vendor\/symfony\/framework-bundle\/Test/',
            '/vendor\/symfony\/phpunit-bridge/',
            '/vendor\/symfony\/dom-crawler/',
            '/vendor\/symfony\/browser-kit/',
            '/vendor\/symfony\/css-selector/',
            '/vendor\/bin\/.phpunit/',
            '/bin\/.phpunit/',
            '/vendor\/bin\/simple-phpunit/',
            '/bin\/phpunit/',
            '/vendor\/coduo\/php-matcher\/src\/PHPUnit/',
            '/vendor\/sulu\/sulu\/src\/Sulu\/Bundle\/TestBundle\/Testing/',
            '/vendor\/webmozart\/assert/',
        ]);

        /** @var \Throwable $throwable */
        $inspector = new Inspector($throwable);

        $writer->write($inspector);

        $this->output->writeln('');
    }

    /**
     * Returns the title contents.
     */
    private function titleLineFrom(string $fg, string $bg, string $title, string $testCaseName): string
    {
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
        if (! empty($warning)) {
            if (! str_contains($warning, "\n")) {
                $warning = sprintf(
                    ' → %s',
                    $warning
                );
            } else {
                $warningLines = explode("\n", $warning);
                $warning = '';

                foreach ($warningLines as $w) {
                    $warning .= sprintf(
                        "\n    <fg=yellow;options=bold>⇂ %s</>",
                        trim($w)
                    );
                }
            }
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
