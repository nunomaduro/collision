<?php

declare(strict_types=1);

namespace NunoMaduro\Collision;

use Felix\Tin\Line;
use Felix\Tin\Themes\OneDark;
use Felix\Tin\Tin;

/**
 * @internal
 */
final class Highlighter
{
    public const ACTUAL_LINE_MARK = 'actual_line_mark';

    public const LINE_NUMBER = 'line_number';

    private const ARROW_SYMBOL = '>';

    private const DELIMITER = '|';

    private const ARROW_SYMBOL_UTF8 = '➜';

    private const DELIMITER_UTF8 = '▕'; // '▶';

    private const LINE_NUMBER_DIVIDER = 'line_divider';

    private const MARKED_LINE_NUMBER = 'marked_line';

    /**
     * Holds the theme.
     */
    private const THEME = [
        self::ACTUAL_LINE_MARK => ['red', 'bold'],
        self::LINE_NUMBER => ['dark_gray'],
        self::MARKED_LINE_NUMBER => ['italic', 'bold'],
        self::LINE_NUMBER_DIVIDER => ['dark_gray'],
    ];

    private const DEFAULT_THEME = [
        self::ACTUAL_LINE_MARK => 'dark_gray',
        self::LINE_NUMBER => 'dark_gray',
        self::MARKED_LINE_NUMBER => 'dark_gray',
        self::LINE_NUMBER_DIVIDER => 'dark_gray',
    ];

    private ConsoleColor $color;

    private Tin $tin;

    private string $delimiter = self::DELIMITER_UTF8;

    private string $arrow = self::ARROW_SYMBOL_UTF8;

    /**
     * Creates an instance of the Highlighter.
     */
    public function __construct(ConsoleColor $color = null, bool $UTF8 = true)
    {
        $this->color = $color ?: new ConsoleColor();
        $this->tin = Tin::from(OneDark::class, $this->color->isSupported());

        foreach (self::DEFAULT_THEME as $name => $styles) {
            if (! $this->color->hasTheme($name)) {
                $this->color->addTheme($name, $styles);
            }
        }

        foreach (self::THEME as $name => $styles) {
            $this->color->addTheme($name, $styles);
        }
        if (! $UTF8) {
            $this->delimiter = self::DELIMITER;
            $this->arrow = self::ARROW_SYMBOL;
        }
        $this->delimiter .= ' ';
    }

    /**
     * Highlights the provided content.
     */
    public function highlight(string $content, ?int $highlightedLine): string
    {
        // handle case where $hlLine is null
        $hl = $this->tin->process($content, function (Line $line) use ($highlightedLine) {
            if ($highlightedLine - $line->number > 4 || $line->number - $highlightedLine > 4) {
                return null;
            }

            if ($line->number === $highlightedLine) {
                return $this->processHighlightedLine($line);
            }

            if ($line->number === $line->totalCount - 1 && $line->toString() === '') {
                return null;
            }

            return $this->processLine($line);
        });

        // remove the extra line at the end
        return substr($hl, 0, -1);
    }

    private function processHighlightedLine(Line $line): string
    {
        return ''.
            $this->lineMark().
            $this->coloredLineNumber(self::MARKED_LINE_NUMBER, $line->number, $line->totalCount).
            $this->lineDelimiter().
            $line->toString().$line->output->newLine();
    }

    public function lineMark(): string
    {
        return $this->color->apply(self::ACTUAL_LINE_MARK, '  '.$this->arrow.'  ');
    }

    private function coloredLineNumber(string $style, int $i, int $length): string
    {
        return $this->color->apply(
            $style,
            str_pad((string) $i, strlen((string) $length), ' ', STR_PAD_LEFT)
        );
    }

    public function lineDelimiter(): string
    {
        return $this->color->apply(self::LINE_NUMBER_DIVIDER, $this->delimiter);
    }

    private function processLine(Line $line): string
    {
        return '     '.
            $this->coloredLineNumber(self::LINE_NUMBER, $line->number, $line->totalCount).
            $this->lineDelimiter().
            $line->toString().$line->output->newLine();
    }
}
