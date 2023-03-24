<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Exceptions;

use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\ExpectationFailedException;
use ReflectionClass;

/**
 * @internal
 */
final class TestException
{
    private const DIFF_SEPARATOR = '--- Expected'.PHP_EOL.'+++ Actual'.PHP_EOL.'@@ @@'.PHP_EOL;

    /**
     * Creates a new Exception instance.
     */
    public function __construct(
        private readonly Throwable $throwable,
        private readonly bool $isVerbose
    ) {
        //
    }

    public function getThrowable(): Throwable
    {
        return $this->throwable;
    }

    /**
     * @return class-string
     */
    public function getClassName(): string
    {
        return $this->throwable->className();
    }

    public function getMessage(): string
    {
        if ($this->throwable->className() === ExpectationFailedException::class) {
            $message = $this->throwable->description();
        } else {
            $message = $this->throwable->message();
        }

        // Contains...
        $re = '/Failed asserting that \'(.*)\' contains "(.*)"\./s';

        preg_match($re, $message, $matches, PREG_OFFSET_CAPTURE, 0);

        if (count($matches) === 3) {
            $actual = $matches[1][0];
            $expected = $matches[2][0];

            $actualExploded = explode(PHP_EOL, $actual);
            $expectedExploded = explode(PHP_EOL, $expected);

            if (($countActual = count($actualExploded)) > 4 && ! $this->isVerbose) {
                $actualExploded = array_slice($actualExploded, 0, 3);
            }

            if (($countExpected = count($expectedExploded)) > 4 && ! $this->isVerbose) {
                $expectedExploded = array_slice($expectedExploded, 0, 3);
            }

            $actualAsString = '';
            $expectedAsString = '';
            foreach ($actualExploded as $line) {
                $actualAsString .= PHP_EOL.$this->colorizeLine($line, 'red');
            }

            foreach ($expectedExploded as $line) {
                $expectedAsString .= PHP_EOL.$this->colorizeLine($line, 'green');
            }

            if ($countActual > 4 && ! $this->isVerbose) {
                $actualAsString .= PHP_EOL.$this->colorizeLine(sprintf('... (%s more lines)', $countActual), 'gray');
            }

            if ($countExpected > 4 && ! $this->isVerbose) {
                $expectedAsString .= PHP_EOL.$this->colorizeLine(sprintf('... (%s more lines)', $countExpected), 'gray');
            }

            $message = implode(PHP_EOL, [
                'Expected: '.ltrim($actualAsString, PHP_EOL.'  '),
                '',
                '  To contain: '.ltrim($expectedAsString, PHP_EOL.'  '),
                '',
            ]);
        }

        // Diffs...
        if (str_contains($message, self::DIFF_SEPARATOR)) {
            $diff = '';
            $lines = explode(PHP_EOL, explode(self::DIFF_SEPARATOR, $message)[1]);

            foreach ($lines as $line) {
                $diff .= $this->colorizeLine($line, str_starts_with($line, '-') ? 'red' : 'green').PHP_EOL;
            }

            $message = str_replace(explode(self::DIFF_SEPARATOR, $message)[1], $diff, $message);
            $message = str_replace(self::DIFF_SEPARATOR, '', $message);
        }

        return $message;
    }

    public function getCode(): int
    {
        return 0;
    }

    /**
     * @throws \ReflectionException
     */
    public function getFile(): string
    {
        if (! isset($this->getTrace()[0])) {
            return (string) (new ReflectionClass($this->getClassName()))->getFileName();
        }

        return $this->getTrace()[0]['file'];
    }

    public function getLine(): int
    {
        if (! isset($this->getTrace()[0])) {
            return 0;
        }

        return (int) $this->getTrace()[0]['line'];
    }

    public function getTrace(): array
    {
        $frames = explode("\n", $this->getTraceAsString());

        $frames = array_filter($frames, fn ($trace) => $trace !== '');

        return array_map(function ($trace) {
            if (trim($trace) === '') {
                return null;
            }

            $parts = explode(':', $trace);
            $line = array_pop($parts);
            $file = implode(':', $parts);

            return [
                'file' => $file,
                'line' => $line,
            ];
        }, $frames);
    }

    public function getTraceAsString(): string
    {
        return $this->throwable->stackTrace();
    }

    public function getPrevious(): ?self
    {
        if ($this->throwable->hasPrevious()) {
            return new self($this->throwable->previous(), $this->isVerbose);
        }

        return null;
    }

    public function __toString()
    {
        return $this->getMessage();
    }

    private function colorizeLine(string $line, string $color): string
    {
        return sprintf('  <fg=%s>%s</>', $color, $line);
    }
}
