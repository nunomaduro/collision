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

use Silex\Application;
use Whoops\Handler\Handler;

class SilexApplicationHandler extends Handler
{
    private $application;

    public function __construct(Application $application)
    {
        $this->application = $application;
    }

    /**
     * @inherit
     */
    public function handle()
    {
        $app = $this->application;
        $errorPageHandler = $app['collision.error_page_handler'];
    }
}
