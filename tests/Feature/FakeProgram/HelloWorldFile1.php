<?php

namespace Tests\Feature\FakeProgram;

class HelloWorldFile1
{
    public static function say()
    {
        HelloWorldFile2::say();
    }
}