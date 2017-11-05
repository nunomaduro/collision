<?php

/*
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Contracts;

use Whoops\Handler\HandlerInterface;

/**
 * This is an Collision Provider implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
interface Provider
{
    /**
     * Registers the current Handler as Error Handler.
     *
     * @return \NunoMaduro\Collision\Contracts\Provider
     */
    public function register(): self;

    /**
     * Returns the handler.
     *
     * @return \Whoops\Handler\HandlerInterface
     */
    public function getHandler(): HandlerInterface;
}
