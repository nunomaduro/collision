<?php

declare(strict_types=1);

namespace Tests\FakeProgram;

class HelloWorldFile2
{
    public static function say()
    {
        return HelloWorldFile3::say();
    }
}
