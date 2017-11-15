<?php
/**
 * This file is part of Collision.
 *
 * (c) Nuno Maduro <enunomaduro@gmail.com>
 *
 *  For the full copyright and license information, please view the LICENSE
 *  file that was distributed with this source code.
 */

namespace NunoMaduro\Collision\Adapters\Pimple;

use Pimple\Container;
use Pimple\ServiceProviderInterface;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\HttpException;
use NunoMaduro\Collision\Handler;
use Whoops\Run;

class CollisionServiceProvider implements ServiceProviderInterface
{
    public function register(Container $container)
    {
        $this->registerErrorPageHandler($container);
        $this->registerExceptionHandler($container);
        $this->registerCollision($container);

        if (set_exception_handler($container['collision.exception_handler']) !== null) {
            restore_exception_handler();
        }

        $container['collision']->register();
    }

    private function registerCollision(Container $container)
    {
        $container['collision'] = function (Container $container) {
            $run = new Run;
            $run->allowQuit(false);
            $run->pushHandler($container['collision.error_page_handler']);
            return $run;
        };
    }

    private function registerErrorPageHandler(Container $container)
    {
        $container['collision.error_page_handler'] = function () {
            return new Handler;
        };
    }

    private function registerExceptionHandler(Container $container)
    {
        $container['collision.exception_handler'] = $container->protect(function ($e) use ($container) {
            $method = Run::EXCEPTION_HANDLER;
            ob_start();
            $container['collision']->$method($e);
            $response = ob_get_clean();
            $code = $e instanceof HttpException ? $e->getStatusCode() : 500;
            return new Response($response, $code);
        });
    }
}
