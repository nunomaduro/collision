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

use JakubOnderka\PhpConsoleColor\ConsoleColor;
use Symfony\Component\Console\Output\OutputInterface;
use JakubOnderka\PhpConsoleHighlighter\Highlighter as BaseHighlighter;
use NunoMaduro\Collision\Contracts\Highlighter as HighlighterContract;

/**
 * This is an Collision Highlighter implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class Highlighter implements HighlighterContract
{
    /**
     * Holds an instance of the
     * base Highlighter.
     *
     * @var \JakubOnderka\PhpConsoleHighlighter\Highlighter
     */
    protected $baseHighlighter;

    /**
     * Creates an instance of the Highlighter.
     *
     * @param \JakubOnderka\PhpConsoleHighlighter\Highlighter|null $baseHighlighter
     */
    public function __construct(BaseHighlighter $baseHighlighter = null)
    {
        $this->baseHighlighter = new BaseHighlighter(new ConsoleColor);
    }
    /**
     * {@inheritdoc}
     */
    public function highlight(string $content, int $line): string
    {
        return $this->baseHighlighter->getCodeSnippet($content, $line, 3, 3);
    }
}
