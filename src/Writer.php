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

use Whoops\Exception\Frame;
use Whoops\Exception\Inspector;
use Symfony\Component\Console\Output\ConsoleOutput;
use Symfony\Component\Console\Output\OutputInterface;
use \NunoMaduro\Collision\Contracts\Writer as WriterContract;
use \NunoMaduro\Collision\Contracts\ArgumentFormatter as ArgumentFormatterContract;

/**
 * This is an Collision Writer implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class Writer implements WriterContract
{
    /**
     * The number of frames if no verbosity is specified.
     */
    const VERBOSITY_NORMAL_FRAMES = 1;

    /**
     * Holds an instance of the Output.
     *
     * @var \Symfony\Component\Console\Output\OutputInterface
     */
    protected $output;

    /**
     * Holds an instance of the Argument Formatter.
     *
     * @var \NunoMaduro\Collision\Contracts\ArgumentFormatter
     */
    protected $argumentFormatter;

    /**
     * Creates an instance of the writer.
     *
     * @param \Symfony\Component\Console\Output\OutputInterface|null $output
     * @param \NunoMaduro\Collision\Contracts\ArgumentFormatter|null $argumentFormatter
     */
    public function __construct(OutputInterface $output = null, ArgumentFormatterContract $argumentFormatter = null)
    {
        $this->output = $output ?: new ConsoleOutput;
        $this->argumentFormatter = $argumentFormatter ?: new ArgumentFormatter;
    }

    /**
     * {@inheritdoc}
     */
    public function write(Inspector $inspector): void
    {
        $this->renderTitle($inspector);

        $frames = $inspector->getFrames()
            ->getArray();

        $this->renderEditor(array_shift($frames));
        $this->renderTrace($frames);
    }

    /**
     * {@inheritdoc}
     */
    public function setOutput(OutputInterface $output): WriterContract
    {
        $this->output = $output;

        return $this;
    }

    /**
     * Renders the title of the exception.
     *
     * @param \Whoops\Exception\Inspector $inspector
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    protected function renderTitle(Inspector $inspector): WriterContract
    {
        $exception = $inspector->getException();
        $message = $exception->getMessage();
        $class = $inspector->getExceptionName();

        $this->render("<bg=red;options=bold>$class</> : <comment>$message</>");
        $this->render("at <fg=green>".$exception->getFile()."</>".": <fg=green>".$exception->getLine()."</>");

        return $this;
    }

    /**
     * Renders the editor containing the code that was the
     * origin of the exception.
     *
     * @param \Whoops\Exception\Frame $frame
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    protected function renderEditor(Frame $frame): WriterContract
    {
        $range = $frame->getFileLines($frame->getLine() - 5, 10);

        foreach ($range as $k => $code) {
            $line = $k + 1;
            $code = $line === $frame->getLine() ? "<bg=red>$code</>" : $code;
            $this->render("$line: $code", false);
        }

        return $this;
    }

    /**
     * Renders the trace of the exception.
     *
     * @param  array $frames
     *
     * @return \NunoMaduro\Collision\Contracts\Writer
     */
    protected function renderTrace(array $frames): WriterContract
    {
        $this->render('<comment>Exception trace:</comment>');
        foreach ($frames as $i => $frame) {

            if ($i > static::VERBOSITY_NORMAL_FRAMES && $this->output->getVerbosity(
                ) < OutputInterface::VERBOSITY_VERBOSE) {
                $this->render('<info>Please use the argument <fg=red>-v</> to see all trace.</info>');
                break;
            }

            $file = $frame->getFile();
            $line = $frame->getLine();
            $class = empty($frame->getClass()) ? '' : $frame->getClass().'::';
            $function = $frame->getFunction();
            $args = $this->argumentFormatter->format($frame->getArgs());
            $pos = str_pad($i + 1, 4, ' ');

            $this->render("<comment><fg=cyan>$pos</>$class$function($args)</comment>");
            $this->render("    <fg=green>$file</> : <fg=green>$line</>", false);
        }

        return $this;
    }

    /**
     * Renders an message into the console.
     *
     * @param  string $message
     * @param  bool $break
     *
     * @return $this
     */
    protected function render(string $message, bool $break = true): WriterContract
    {
        if ($break) {
            $this->output->writeln("");
        }

        $this->output->writeln("  $message");

        return $this;
    }
}
