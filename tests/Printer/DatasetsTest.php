<?php

declare(strict_types=1);

namespace Tests\Printer;

use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class DatasetsTest extends TestCase
{
    public static function provideData(): array
    {
        return [
            'a' => ['Foo'],
            'b' => ['Bar'],
        ];
    }

    #[DataProvider('provideData')]
    public function testWithOutput(string $data)
    {
        echo $data;

        $this->assertTrue(true);
    }
}
