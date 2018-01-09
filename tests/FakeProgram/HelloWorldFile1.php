<?php

namespace Tests\FakeProgram;

class HelloWorldFile1
{
    public static function say()
    {
        return HelloWorldFile2::say();
    }
}
