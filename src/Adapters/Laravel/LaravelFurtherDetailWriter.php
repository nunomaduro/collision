<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use Illuminate\Validation\ValidationException;
use NunoMaduro\Collision\Contracts\FurtherDetailWriter;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class LaravelFurtherDetailWriter implements FurtherDetailWriter
{
    public function write(OutputInterface $output, Throwable $throwable): void
    {
        if ($throwable instanceof ValidationException) {
            $this->handleValidationException($output, $throwable);
        }
    }

    public function handleValidationException(OutputInterface $output, ValidationException $throwable): void
    {
        $output->writeln('');
        $output->writeln('  The exception contains the following validation errors:');
        $output->writeln('');
        $output->writeln('  [');

        foreach ($throwable->errors() as $key => $errors) {
            $output->writeln("    <fg=red>'{$key}'</> => [");
            foreach ($errors as $message) {
                $output->writeln("      <fg=default>'{$message}'</>,");
            }
            $isLastError = array_reverse(array_keys($throwable->errors()))[0] === $key;
            $output->writeln($isLastError ? '    ]' : '    ],');
        }

        $output->writeln('  ];');
        $output->writeln('');
    }
}
