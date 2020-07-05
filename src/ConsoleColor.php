<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision;

use NunoMaduro\Collision\Exceptions\InvalidStyleException;
use NunoMaduro\Collision\Exceptions\ShouldNotHappen;

/**
 * This is an Collision Console Color implementation.
 *
 * Code originally from { JakubOnderka\\PhpConsoleColor }. But the package got deprecated.
 *
 * @internal
 *
 * @final
 */
class ConsoleColor
{
    const FOREGROUND = 38;
    const BACKGROUND = 48;

    const COLOR256_REGEXP = '~^(bg_)?color_([0-9]{1,3})$~';

    const RESET_STYLE = 0;

    /** @var bool */
    private $isSupported;

    /** @var bool */
    private $forceStyle = false;

    /** @var array<string,string|null> */
    private $styles = [
        'none'      => null,
        'bold'      => '1',
        'dark'      => '2',
        'italic'    => '3',
        'underline' => '4',
        'blink'     => '5',
        'reverse'   => '7',
        'concealed' => '8',

        'default'    => '39',
        'black'      => '30',
        'red'        => '31',
        'green'      => '32',
        'yellow'     => '33',
        'blue'       => '34',
        'magenta'    => '35',
        'cyan'       => '36',
        'light_gray' => '37',

        'dark_gray'     => '90',
        'light_red'     => '91',
        'light_green'   => '92',
        'light_yellow'  => '93',
        'light_blue'    => '94',
        'light_magenta' => '95',
        'light_cyan'    => '96',
        'white'         => '97',

        'bg_default'    => '49',
        'bg_black'      => '40',
        'bg_red'        => '41',
        'bg_green'      => '42',
        'bg_yellow'     => '43',
        'bg_blue'       => '44',
        'bg_magenta'    => '45',
        'bg_cyan'       => '46',
        'bg_light_gray' => '47',

        'bg_dark_gray'     => '100',
        'bg_light_red'     => '101',
        'bg_light_green'   => '102',
        'bg_light_yellow'  => '103',
        'bg_light_blue'    => '104',
        'bg_light_magenta' => '105',
        'bg_light_cyan'    => '106',
        'bg_white'         => '107',
    ];

    /** @var array<string,string[]> */
    private $themes = [];

    public function __construct()
    {
        $this->isSupported = $this->isSupported();
    }

    /**
     * @param string|string[] $style
     *
     * @return string
     *
     * @throws InvalidStyleException
     */
    public function apply($style, string $text)
    {
        if (!$this->isStyleForced() && !$this->isSupported()) {
            return $text;
        }

        if (is_string($style)) {
            $style = [$style];
        }

        if (!is_array($style)) {
            throw new \TypeError(sprintf('%s::apply(): Argument #1 ($style) must be of type string|array, %s given', self::class, get_debug_type($style)));
        }

        $sequences = [];

        foreach ($style as $s) {
            if (isset($this->themes[$s])) {
                $sequences = array_merge($sequences, $this->themeSequence($s));
            } elseif ($this->isValidStyle($s)) {
                $sequences[] = $this->styleSequence($s);
            } else {
                throw new ShouldNotHappen();
            }
        }

        $sequences = array_filter($sequences, function ($val) {
            return $val !== null;
        });

        if (empty($sequences)) {
            return $text;
        }

        return self::escSequence(implode(';', $sequences)) . $text . self::escSequence(self::RESET_STYLE);
    }

    /**
     * @return void
     */
    public function setForceStyle(bool $forceStyle)
    {
        $this->forceStyle = (bool) $forceStyle;
    }

    /**
     * @return bool
     */
    public function isStyleForced()
    {
        return $this->forceStyle;
    }

    /**
     * @param array<string,string[]> $themes
     *
     * @return void
     */
    public function setThemes(array $themes)
    {
        $this->themes = [];
        foreach ($themes as $name => $styles) {
            $this->addTheme($name, $styles);
        }
    }

    /**
     * @param string|string[] $styles
     *
     * @return void
     *
     * @throws InvalidStyleException
     */
    public function addTheme(string $name, $styles)
    {
        if (is_string($styles)) {
            $styles = [$styles];
        }
        if (!is_array($styles)) {
            throw new \TypeError(sprintf('%s::addTheme(): Argument #2 ($styles) must be of type string|array, %s given', self::class, get_debug_type($styles)));
        }

        foreach ($styles as $style) {
            if (!$this->isValidStyle($style)) {
                throw new InvalidStyleException($style);
            }
        }

        $this->themes[$name] = $styles;
    }

    /**
     * @return array
     */
    public function getThemes()
    {
        return $this->themes;
    }

    /**
     * @return bool
     */
    public function hasTheme(string $name)
    {
        return isset($this->themes[$name]);
    }

    /**
     * @return void
     */
    public function removeTheme(string $name)
    {
        unset($this->themes[$name]);
    }

    /**
     * @return bool
     */
    public function isSupported()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            if (function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT)) {
                return true;
            }

            if (isset($_SERVER['ANSICON']) || ($_SERVER['ConEmuANSI'] ?? '') === 'ON') {
                return true;
            }

            return false;
        }

        return function_exists('posix_isatty') && @posix_isatty(STDOUT);
    }

    /**
     * @return bool
     */
    public function are256ColorsSupported()
    {
        if (DIRECTORY_SEPARATOR === '\\') {
            return function_exists('sapi_windows_vt100_support') && @sapi_windows_vt100_support(STDOUT);
        }

        return strpos((string) $_SERVER['TERM'] ?? '', '256color') !== false;
    }

    /**
     * @return array
     */
    public function getPossibleStyles()
    {
        return array_keys($this->styles);
    }

    /**
     * @return array<string|null>
     */
    private function themeSequence(string $name)
    {
        $sequences = [];
        foreach ($this->themes[$name] as $style) {
            $sequences[] = $this->styleSequence($style);
        }

        return $sequences;
    }

    /**
     * @return string|null
     */
    private function styleSequence(string $style)
    {
        if (array_key_exists($style, $this->styles)) {
            return $this->styles[$style];
        }

        if (!$this->are256ColorsSupported()) {
            return null;
        }

        preg_match(self::COLOR256_REGEXP, $style, $matches);

        $type  = $matches[1] === 'bg_' ? self::BACKGROUND : self::FOREGROUND;
        $value = $matches[2];

        return "$type;5;$value";
    }

    /**
     * @return bool
     */
    private function isValidStyle(string $style)
    {
        return array_key_exists($style, $this->styles) || preg_match(self::COLOR256_REGEXP, $style);
    }

    /**
     * @param string|int $value
     *
     * @return string
     */
    private static function escSequence($value)
    {
        return "\033[{$value}m";
    }
}
