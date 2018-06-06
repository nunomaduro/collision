<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

use Symfony\Component\Console\ConsoleEvents;
use NunoMaduro\Collision\Adapters\Symfony\EventListener\ErrorListener;

$container->autowire('collision.error_listener', ErrorListener::class)
    ->addTag('kernel.event_listener', ['event' => ConsoleEvents::ERROR]);
