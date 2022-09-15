<?php

declare(strict_types=1);

namespace Tests\Printer;

use PHPUnit\Framework\TestCase;

class DatasetsTest extends TestCase
{
    public function provideData(): array
    {
        return [
            'a' => ['Foo'],
            'b' => ['Bar'],
        ];
    }

    /**
     * @dataProvider provideData
     */
    public function testWithOutput(string $data)
    {
        echo $data;

        $this->assertTrue(true);
    }
}
