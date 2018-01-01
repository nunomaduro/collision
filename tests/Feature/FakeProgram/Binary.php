<?php

require __DIR__.'/../../../vendor/autoload.php';

(new \NunoMaduro\Collision\Provider())->register();

\Tests\Feature\FakeProgram\HelloWorldFile1::say();
