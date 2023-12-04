<?php

declare(strict_types=1);

namespace NunoMaduro\Collision\Adapters\Laravel;

use NunoMaduro\Collision\Contracts\SolutionsRepository;
use Spatie\Ignition\Contracts\SolutionProviderRepository;
use Throwable;

/**
 * @internal
 */
final class IgnitionSolutionsRepository implements SolutionsRepository
{
    /**
     * Holds an instance of ignition solutions provider repository.
     *
     * @var \Spatie\Ignition\Contracts\SolutionProviderRepository
     */
    protected $solutionProviderRepository; // @phpstan-ignore-line

    /**
     * IgnitionSolutionsRepository constructor.
     */
    public function __construct(SolutionProviderRepository $solutionProviderRepository) // @phpstan-ignore-line
    {
        $this->solutionProviderRepository = $solutionProviderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromThrowable(Throwable $throwable): array // @phpstan-ignore-line
    {
        return $this->solutionProviderRepository->getSolutionsForThrowable($throwable); // @phpstan-ignore-line
    }
}
