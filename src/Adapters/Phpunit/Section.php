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
use PHPUnit\Framework\TestSuite;
use PHPUnit\Framework\Warning;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\ConsoleSectionOutput;
use Throwable;

/**
 * @internal
 */
final class Section
{
    /**
     * Holds an instance of the section.
     *
     * @var ConsoleSectionOutput
     */
    private $section;

    /**
     * Holds an instance of the test suite.
     *
     * @var TestSuite
     */
    private $testSuite;

    /**
     * If the current testSuite is dirty.
     *
     * @var bool
     */
    private $dirty = false;

    /**
     * Holds an instance of the test case.
     *
     * @var TestCase
     */
    private $testCase;

    /**
     * If the current testCase should pass.
     *
     * @var bool
     */
    private $shouldPass = true;

    /**
     * Holds the content of the section.
     *
     * @var array<int, string>
     */
    private $tests = [];

    /**
     * Section constructor.
     *
     * @param  ConsoleSectionOutput  $section
     * @param  TestSuite  $testSuite
     */
    public function __construct(ConsoleSectionOutput $section, TestSuite $testSuite)
    {
        $this->section = $section;
        $this->testSuite = $testSuite;
        $this->testCase = new class extends TestCase {
        };
    }

    /**
     * @param  ConsoleOutput  $output
     * @param  TestSuite  $testSuite
     *
     * @return Section
     */
    public static function create(ConsoleOutput $output, TestSuite $testSuite): Section
    {
        return new self($output->section(), $testSuite);
    }

    /**
     * Runs the given test.
     *
     * @param  TestCase  $test
     *
     * @return void
     */
    public function runs(TestCase $test): void
    {
        $this->testCase = $test;
        $this->shouldPass = true;

        if (count($this->tests) === 0) {
            $this->title('RUNS', 'yellow');
        }

        $this->updateTest('•', 'yellow', true);
    }

    /**
     * Passes the current test case.
     *
     * @return void
     */
    public function pass(): void
    {
        if ($this->shouldPass) {
            $this->updateTest('✓', 'green');
        }
    }

    /**
     * Marks the current test case as failed.
     *
     * @return void
     */
    public function fail(): void
    {
        $this->title('FAIL', 'red');
        $this->updateTest('✕', 'red');

        $this->dirty = true;
        $this->shouldPass = false;
    }

    /**
     * Marks the current test case as incomplete.
     *
     * @param  Throwable  $throwable
     *
     * @return void
     */
    public function incomplete(Throwable $throwable): void
    {
        $this->updateTest('i', 'yellow', false, $throwable->getMessage());
        $this->title('WARN', 'yellow');

        $this->dirty = true;
        $this->shouldPass = false;
    }

    /**
     * Marks the current test case as risky.
     *
     * @return void
     */
    public function risky(): void
    {
        $this->updateTest('r', 'yellow');
        $this->title('WARN', 'yellow');

        $this->dirty = true;
        $this->shouldPass = false;
    }

    /**
     * Marks the current test case as risky.
     *
     * @param  Throwable  $throwable
     *
     * @return void
     */
    public function skipped(Throwable $throwable): void
    {
        $this->updateTest('s', 'yellow', false, $throwable->getMessage());
        $this->title('WARN', 'yellow');

        $this->dirty = true;
        $this->shouldPass = false;
    }

    /**
     * Marks the current test case as risky.
     *
     * @return void
     */
    public function warn(Warning $warning): void
    {
        $this->updateTest('w', 'yellow', false, $warning->getMessage());
        $this->title('WARN', 'yellow');

        $this->dirty = true;
        $this->shouldPass = false;
    }

    public function end(): void
    {
        if (! $this->dirty && count($this->tests)) {
            $this->title('PASS', 'green');
        }
    }

    /**
     * Updates the console with the current state.
     *
     * @param  string  $icon
     * @param  string  $color
     *
     * @param  bool  $create
     *
     * @return void
     */
    private function updateTest(string $icon, string $color, bool $create = false, string $note = null): void
    {
        $value = sprintf(
            '  <fg=%s;options=bold>%s</><fg=default> %s</>',
            $color,
            $icon,
            $name = $this->getTestCaseDescription()
        );

        if ($note) {
            $value .= sprintf(' → <fg=%s>%s</>', $color, trim((string) preg_replace("/\r|\n/", ' ', $note)));
        }

        if ($create) {
            $this->tests[] = $value;
        } else {
            $this->tests[count($this->tests) - 1] = $value;
        }

        $this->update();
    }

    /**
     * Updates the console with the current state.
     *
     * @return void
     */
    private function update(): void
    {
        $this->section->clear();
        $this->section->writeln($this->tests);
    }

    /**
     * Get the current test case description.
     *
     * @return string
     */
    private function getTestCaseDescription(): string
    {
        $name = $this->testCase->getName(false);

        // First, lets replace underscore by spaces.
        $name = str_replace('_', ' ', $name);

        // Then, replace upper cases by spaces.
        $name = (string) preg_replace('/([A-Z])/', ' $1', $name);

        // Finally, if it starts with `test`, we remove it.
        $name = (string) preg_replace('/^test/', '', $name);

        // Removes spaces
        $name = (string) trim($name);

        // Finally, lower case everything
        return (string) mb_strtolower($name);
    }

    private function title(string $title, string $color): void
    {
        $fg = $title === 'FAIL' ? 'default' : 'black';

        $classParts = explode('\\', get_class($this->testCase));

        // Removes `Tests` part
        array_shift($classParts);

        $highlightedPart = array_pop($classParts);
        $nonHighlightedPart = implode('\\', $classParts);
        $class = sprintf("\e[2m%s\e[22m<fg=white;options=bold>%s</>", "$nonHighlightedPart\\", $highlightedPart);

        $this->tests[0] = sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>",
            $fg,
            $color,
            $title,
            $class
        );

        $this->update();
    }
}
