<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('coverage')]
class CoverageTest extends TestCase
{
    public function testExample()
    {
        $this->assertTrue(true);
    }
}
