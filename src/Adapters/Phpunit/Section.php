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
     * Holds an instance of the console output.
     *
     * @var ConsoleOutput
     */
    private $output;

    /**
     * Holds an instance of the console section.
     *
     * @var ConsoleSectionOutput
     */
    private $section;

    /**
     * Holds an instance of the console section.
     *
     * @var ConsoleSectionOutput
     */
    private $footer;

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
    public $shouldPass = true;

    /**
     * Holds the content of the section.
     *
     * @var array<int, string>
     */
    private $tests = [];

    /**
     * Section constructor.
     *
     * @param  ConsoleOutput  $output
     */
    public function __construct(ConsoleOutput $output)
    {
        $this->output = $output;
        $this->section = $output->section();
        $this->footer = $output->section();
        $this->testCase = new class extends TestCase {
        };
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
        if (get_class($this->testCase) !== get_class($test)) {
            $this->end();
            $this->section = $this->output->section();
            $this->tests = [];
            $this->dirty = false;
        }

        $this->shouldPass = true;
        $this->testCase = $test;
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
        $this->updateTest('✕', 'red');
        $this->title('FAIL', 'red');

        $this->footer->clear();
        $this->section->write($this->tests);

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

        $this->dirty = true;
        $this->shouldPass = false;
    }

    /**
     * Ends the current test suite.
     *
     * Here we do 3 things:
     *
     * 0. Remove the footer.
     * 1. Display the title.
     * 2. Display the tests results.
     */
    public function end(): void
    {
        if (count($this->tests)) {
            $this->footer->clear();

            if (! $this->dirty) {
                $this->title('PASS', 'green');
            } else {
                $this->title('WARN', 'yellow');
            }

            $this->section->write($this->tests);
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
        }

        $this->tests[count($this->tests) - 1] = $value;

        $this->footer('RUNS', 'yellow');
        $this->footer->write($value);
    }

    /**
     * Get the current test case description.
     *
     * @return string
     */
    private function getTestCaseDescription(): string
    {
        $name = $this->testCase->getName(true);

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

    private function footer(string $title, string $color): void
    {
        $fg = $title === 'FAIL' ? 'default' : 'black';

        $classParts = explode('\\', get_class($this->testCase));

        // Removes `Tests` part
        array_shift($classParts);

        $highlightedPart = array_pop($classParts);
        $nonHighlightedPart = implode('\\', $classParts);
        $class = sprintf("\e[2m%s\e[22m<fg=white;options=bold>%s</>", "$nonHighlightedPart\\", $highlightedPart);

        $this->footer->clear();
        $this->footer = $this->output->section();
        $this->footer->write(sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>",
            $fg,
            $color,
            $title,
            $class
        ));
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

        $this->section->write(sprintf(
            "\n  <fg=%s;bg=%s;options=bold> %s </><fg=default> %s</>",
            $fg,
            $color,
            $title,
            $class
        ));
    }
}
