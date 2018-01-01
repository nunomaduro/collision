<?php

namespace Tests\Feature\FakeProgram;

class HelloWorldFile2
{
    public static function say()
    {
        HelloWorldFile3::say();
    }
}
