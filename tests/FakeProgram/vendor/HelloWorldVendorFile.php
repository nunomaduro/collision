<?php

declare(strict_types=1);

namespace Tests\FakeProgram\vendor;

use Tests\FakeProgram\HelloWorldFile3;

class HelloWorldVendorFile
{
    public static function say()
    {
        return HelloWorldFile3::say();
    }
}
