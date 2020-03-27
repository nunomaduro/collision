<?php

namespace Tests\Feature;

use Tests\TestCase;
use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;

class ExampleWithCustomNameTest extends TestCase implements HasPrintableTestCaseName
{
    public function getPrintableTestCaseName(): string
    {
        return __FILE__;
    }

    /**
     * @group custom-name
     */
    public function testPassExample()
    {
        $this->assertTrue(true);
    }
}
