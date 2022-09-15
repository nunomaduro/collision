<?php

declare(strict_types=1);

namespace Tests\Printer;

use PHPUnit\Framework\Assert;
use PHPUnit\Framework\TestCase;

class Hooks extends TestCase
{
    /**
     * This method is called before the first test of this Test Case is run.
     */
    public static function setUpBeforeClass(): void
    {
        parent::setUpBeforeClass();

        Assert::assertFalse(true);
    }

    /** @test */
    public function testDummyA()
    {
        $this->assertTrue(true);
    }

    /** @test */
    public function testDummyB()
    {
        $this->assertTrue(true);
    }
}
