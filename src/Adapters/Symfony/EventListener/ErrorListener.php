<?php

/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Symfony\EventListener;

use Whoops\Exception\Inspector;
use NunoMaduro\Collision\Provider;
use Symfony\Component\Console\Event\ConsoleErrorEvent;

/**
 * This is an Collision Symfony Adapter Error Listener implementation.
 *
 * @author Nuno Maduro <enunomaduro@gmail.com>
 */
class ErrorListener
{
    /**
     * Retreives error from the provided event
     * and ouputs details of that error to the event output.
     *
     * @param \Symfony\Component\Console\Event\ConsoleErrorEvent $event
     */
    public function onConsoleError(ConsoleErrorEvent $event)
    {
        $handler = (new Provider())->register()
            ->getHandler()
            ->setOutput($event->getOutput());

        $handler->setInspector(new Inspector($event->getError()));

        $handler->handle();

        $event->setExitCode(0);
    }
}
