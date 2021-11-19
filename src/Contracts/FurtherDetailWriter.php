<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Contracts;

use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

interface FurtherDetailWriter
{
    public function write(OutputInterface $output, Throwable $throwable): void;
}
