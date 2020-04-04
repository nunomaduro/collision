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
final class TestResult
{
    public const FAIL       = 'failed';
    public const SKIPPED    = 'skipped';
    public const INCOMPLETE = 'incompleted';
    public const RISKY      = 'risked';
    public const WARN       = 'warnings';
    public const RUNS       = 'pending';
    public const PASS       = 'passed';

    /**
     * @readonly
     *
     * @var string
     */
    public $description;

    /**
     * @readonly
     *
     * @var string
     */
    public $type;

    /**
     * @readonly
     *
     * @var string
     */
    public $icon;

    /**
     * @readonly
     *
     * @var string
     */
    public $color;

    /**
     * @readonly
     *
     * @var string|null
     */
    public $warning;

    /**
     * Test constructor.
     *
     * @param string $warning
     */
    private function __construct(string $description, string $type, string $icon, string $color, string $warning = null)
    {
        $this->description = $description;
        $this->type        = $type;
        $this->icon        = $icon;
        $this->color       = $color;
        $this->warning     = trim((string) preg_replace("/\r|\n/", ' ', (string) $warning));
    }

    /**
     * Creates a new test from the given test case.
     */
    public static function fromTestCase(TestCase $testCase, string $type, string $warning = null): self
    {
        $description = self::makeDescription($testCase);

        $icon = self::makeIcon($type);

        $color = self::makeColor($type);

        return new self($description, $type, $icon, $color, $warning);
    }

    /**
     * Get the test case description.
     */
    public static function makeDescription(TestCase $testCase): string
    {
        $name = $testCase->getName(true);

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

    /**
     * Get the test case icon.
     */
    public static function makeIcon(string $type): string
    {
        switch ($type) {
            case self::FAIL:
                return '✕';
            case self::SKIPPED:
                return 's';
            case self::RISKY:
                return 'r';
            case self::INCOMPLETE:
                return 'i';
            case self::WARN:
                return 'w';
            case self::RUNS:
                return '•';
            default:
                return '✓';
        }
    }

    /**
     * Get the test case color.
     */
    public static function makeColor(string $type): string
    {
        switch ($type) {
            case self::FAIL:
                return 'red';
            case self::SKIPPED:
            case self::INCOMPLETE:
            case self::RISKY:
            case self::WARN:
            case self::RUNS:
                return 'yellow';
            default:
                return 'green';
        }
    }
}
