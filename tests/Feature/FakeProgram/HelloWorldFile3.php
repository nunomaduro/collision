<?php

namespace Tests\Feature\FakeProgram;

class HelloWorldFile3
{
    public static function say()
    {
        throw new FakeException('Fail description');
    }
}