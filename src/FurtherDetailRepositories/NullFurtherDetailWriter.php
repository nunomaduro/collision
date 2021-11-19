<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\FurtherDetailRepositories;

use NunoMaduro\Collision\Contracts\FurtherDetailWriter;
use Symfony\Component\Console\Output\OutputInterface;
use Throwable;

final class NullFurtherDetailWriter implements FurtherDetailWriter
{

    public function write(OutputInterface $output, Throwable $throwable): void
    {
    }
}
