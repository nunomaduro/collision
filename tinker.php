<?php

use NunoMaduro\Collision\Provider;

require __DIR__.'/vendor/autoload.php';

(new Provider())->register();

class C
{
    public function c()
    {
        throw new Exception('Hello world');
    }
}

(new C)->c();
