<?php

declare(strict_types=1);

namespace Tests\Feature;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use Tests\TestCase;

class ExampleWithCustomNameTest extends TestCase implements HasPrintableTestCaseName
{
    public function getPrintableTestCaseName(): string
    {
        return 'my-custom-name';
    }

    /**
     * @group custom-name
     */
    public function testPassExample()
    {
        $this->assertTrue(true);
    }
}
