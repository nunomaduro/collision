<?php

declare(strict_types=1);

namespace LaravelApp\tests\Feature;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use Tests\TestCase;

class ExampleWithUnexpectedOutputTest extends TestCase
{
    /**
     * @group unexpected-output
     */
    public function testPassExample()
    {
        echo "This is an unexpected output";

        $this->assertTrue(true);
    }
}
