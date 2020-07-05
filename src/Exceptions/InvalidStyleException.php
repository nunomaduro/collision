<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Exceptions;

use RuntimeException;

/**
 * @internal
 */
final class InvalidStyleException extends RuntimeException
{
    public function __construct(string $style)
    {
        parent::__construct(sprintf('Invalid style [%s].', $style));
    }
}
