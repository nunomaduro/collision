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

use Illuminate\Support\ServiceProvider;
use NunoMaduro\Collision\Contracts\Handler;

/**
 * This is an Collision Argument Formatter implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class CollisionServiceProvider extends ServiceProvider
{
    /**
     * {@inheritdoc}
     */
    public function register()
    {
        $handler = (new Provider)->register()
            ->getHandler();

        $this->app->instance(Handler::class, $handler);
    }
}
