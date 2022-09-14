<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Exceptions;

use PHPUnit\Event\Code\Throwable;
use ReflectionClass;

/**
 * @internal
 */
final class TestException
{
    private const DIFF_SEPARATOR = '  --- Expected'.PHP_EOL.'  +++ Actual'.PHP_EOL.'  @@ @@'.PHP_EOL;

    /**
     * Creates a new Exception instance.
     */
    public function __construct(private readonly Throwable $throwable)
    {
        //
    }

    public function getClassName(): string
    {
        return $this->throwable->className();
    }

    public function getMessage(): string
    {
        $message = rtrim(trim(str_replace(rtrim($this->getTraceAsString(), PHP_EOL), '', $this->throwable->message())), PHP_EOL);

        if (str_contains($message, self::DIFF_SEPARATOR)) {
            $diff = '';
            $lines = explode(PHP_EOL, explode(self::DIFF_SEPARATOR, $message)[1]);

            foreach ($lines as $line) {
                if (0 === strpos($line, '  -')) {
                    $line = '<fg=red>'.$line.'</>';
                } elseif (0 === strpos($line, '  +')) {
                    $line = '<fg=green>'.$line.'</>';
                }

                $diff .= $line.PHP_EOL;
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

    public function getFile(): string
    {
        if (! isset($this->getTrace()[0])) {
            return (new ReflectionClass($this->getClassName()))->getFileName();
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
            if (empty($trace)) {
                return null;
            }
            [$file, $line] = explode(':', $trace);

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
            return new self($this->throwable->previous());
        }

        return null;
    }

    public function __toString()
    {
        return $this->getMessage();
    }
}
