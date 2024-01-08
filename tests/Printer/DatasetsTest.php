<?php

declare(strict_types=1);

namespace Tests\Printer;

use PHPUnit\Framework\TestCase;
use PHPUnit\Framework\Attributes\DataProvider;

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
