<?php

declare(strict_types=1);

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Phpunit\Subscribers;

use NunoMaduro\Collision\Adapters\Phpunit\Printers\DefaultPrinter;

/**
 * @internal
 */
abstract class Subscriber
{
    private DefaultPrinter $printer;

    public function __construct(DefaultPrinter $printer)
    {
        $this->printer = $printer;
    }

    protected function printer(): DefaultPrinter
    {
        return $this->printer;
    }
}
