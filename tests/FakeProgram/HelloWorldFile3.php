<?php

declare(strict_types=1);

namespace Tests\FakeProgram;

class HelloWorldFile3
{
    public static function say()
    {
        return new FakeException('Fail description');
    }
}
