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
final class ShouldNotHappen extends RuntimeException
{
    public function __construct()
    {
        $message = 'This should not happen, please open an issue on collision repository: %s';

        parent::__construct(sprintf($message, 'https://github.com/nunomaduro/collision/issues/new'));
    }
}
