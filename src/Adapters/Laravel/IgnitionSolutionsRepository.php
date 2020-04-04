<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Laravel;

use Facade\IgnitionContracts\SolutionProviderRepository;
use NunoMaduro\Collision\Contracts\SolutionsRepository;
use Throwable;

/**
 * This is an Collision Laravel Adapter Solutions Provider implementation.
 *
 * Registers the Error Handler on Laravel.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class IgnitionSolutionsRepository implements SolutionsRepository
{
    /**
     * Holds an instance of ignition solutions provider repository.
     *
     * @var \Facade\IgnitionContracts\SolutionProviderRepository
     */
    protected $solutionProviderRepository;

    /**
     * IgnitionSolutionsRepository constructor.
     */
    public function __construct(SolutionProviderRepository $solutionProviderRepository)
    {
        $this->solutionProviderRepository = $solutionProviderRepository;
    }

    /**
     * {@inheritdoc}
     */
    public function getFromThrowable(Throwable $throwable): array
    {
        return $this->solutionProviderRepository->getSolutionsForThrowable($throwable);
    }
}
