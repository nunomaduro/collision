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

use Whoops\Util\Misc;

/**
 * This is an Collision Laravel Inspector implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class Inspector extends \Whoops\Exception\Inspector
{
    /**
     * {@inheritdoc}
     */
    protected function getTrace($e)
    {
        return $e->getTrace();
    }
}
