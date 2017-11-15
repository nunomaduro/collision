<?php
/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Silex;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Silex\Application;
use Silex\Api\EventListenerProviderInterface;
use Silex\ExceptionListenerWrapper;
use Symfony\Component\EventDispatcher\EventDispatcherInterface;
use Symfony\Component\HttpKernel\KernelEvents;
use NunoMaduro\Collision\Adapters\Pimple\CollisionServiceProvider as PimpleCollisionServiceProvider;

class CollisionServiceProvider implements ServiceProviderInterface, EventListenerProviderInterface
{
    public function register(Container $container)
    {
        $container->register(new PimpleCollisionServiceProvider);
        if ($container instanceof Application) {
            $container['collision']->pushHandler(new SilexApplicationHandler($container));
        }
    }

    public function subscribe(Container $container, EventDispatcherInterface $dispatcher)
    {
        if ($container instanceof Application) {
            $dispatcher->addListener(
                KernelEvents::EXCEPTION,
                new ExceptionListenerWrapper($container, $container['collision.exception_handler']),
                -1
            );
        }
    }
}
