<?php

declare(strict_types=1);

namespace Tests\Feature;

use NunoMaduro\Collision\Contracts\Adapters\Phpunit\HasPrintableTestCaseName;
use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

class ExampleWithCustomNameTest extends TestCase implements HasPrintableTestCaseName
{
    public static function getPrintableTestCaseName(): string
    {
        return 'my-custom-test-case-name';
    }

    public function getPrintableTestCaseMethodName(): string
    {
        return 'my-custom-test-case-method-name';
    }

    public static function getLatestPrintableTestCaseMethodName(): string
    {
        return 'my-custom-test-case-name';
    }

    #[Group('custom-name')]
    public function testPassExample()
    {
        $this->assertTrue(true);
    }
}
