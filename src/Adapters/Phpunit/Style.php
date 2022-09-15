<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Phpunit;

use NunoMaduro\Collision\Exceptions\ShouldNotHappen;
use NunoMaduro\Collision\Exceptions\TestException;
use NunoMaduro\Collision\Writer;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Event\Telemetry\Info;
use PHPUnit\Framework\ExpectationFailedException;
use PHPUnit\TestRunner\TestResult\Facade;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleOutputInterface;
use function Termwind\render;
use function Termwind\renderUsing;
use Whoops\Exception\Inspector;

/**
 * @internal
 */
final class Style
{
    private readonly \Symfony\Component\Console\Output\ConsoleOutput $output;

    private float $previousDurationSinceStart = 0.0;

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
                $this->writeDescriptionLine($testResult);
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
            $this->output->writeln(['']);
        }

        array_map(function (TestResult $testResult): void {
            if (! $testResult->throwable instanceof Throwable) {
                throw new ShouldNotHappen();
            }

            renderUsing($this->output);

            render(<<<'HTML'
                <div class="mx-2 text-red text-right">
                    <hr/>
                </div>
            HTML);

            $testCaseName = $testResult->testCaseName;
            $description = $testResult->description;

            $throwableClassName = $testResult->throwable->className();

            $throwableClassName = $throwableClassName !== ExpectationFailedException::class
                ? sprintf('<span class="px-1 bg-red font-bold">%s</span>', $throwableClassName)
                : '';

            render(sprintf(<<<'HTML'
                <div class="flex justify-between mx-2">
                    <span>
                        <span class="px-1 bg-red font-bold">FAIL</span> <span class="font-bold">%s</span><span class="text-gray mx-1">></span><span>%s</span>
                    </span>
                    <span>
                        %s
                    </span>
                </div>
            HTML, $testCaseName, $description, $throwableClassName));

            $this->writeError($testResult->throwable);
        }, $errors);
    }

    /**
     * Writes the final recap.
     */
    public function writeRecap(State $state, Info $telemetry): void
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

        $timeElapsed = number_format($telemetry->durationSinceStart()->asFloat(), 2, '.', '');
        $this->output->writeln([
            '',
            sprintf(
                '  <fg=gray;options=bold>Duration:</> <fg=default>%ss</>',
                $timeElapsed
            ),
        ]
        );

        if (! empty($tests)) {
            $this->output->writeln([
                sprintf(
                    '  <fg=gray;options=bold>Tests:</>    <fg=default>%s</><fg=gray> (%s assertions)</>',
                    implode('<fg=gray>,</> ', $tests),
                    Facade::result()->numberOfAssertions()
                ),
            ]);
        }

        $this->output->writeln('');
    }

    /**
     * Displays the error using Collision's writer
     * and terminates with exit code === 1.
     */
    public function writeError(Throwable $throwable): void
    {
        $writer = (new Writer())->setOutput($this->output);

        $throwable = new TestException($throwable);

        $writer->showTitle(false);

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
     * Writes a description line.
     */
    private function writeDescriptionLine(TestResult $result): void
    {
        if (! empty($warning = $result->warning)) {
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

        if (is_null($result->telemetry)) {
            throw new ShouldNotHappen();
        }

        $duration = $result->telemetry->durationSinceStart()->asFloat() - $this->previousDurationSinceStart;

        $seconds = number_format($duration, 2, '.', '');
        $seconds = $seconds !== '0.00' ? sprintf('%ss', $seconds) : '';

        // Pest specific
        if (isset($_ENV['REBUILD_SNAPSHOTS'])) {
            $seconds = '';
        }

        renderUsing($this->output);
        render(sprintf(<<<'HTML'
            <div class="flex justify-between mx-2">
                <span>
                    <span class="text-%s font-bold">%s</span><span class="ml-1 text-gray-500">%s</span><span class="ml-1 text-yellow">%s</span>
                </span>
                <span class="text-gray-600">
                    %s
                </span>
            </div>
        HTML, $result->color, $result->icon, $result->description, $warning, $seconds));

        $this->previousDurationSinceStart = $result->telemetry->durationSinceStart()->asFloat();
    }
}
