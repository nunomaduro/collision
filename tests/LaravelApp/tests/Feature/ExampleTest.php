<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

class ExampleTest extends TestCase
{
    public function testSkippedExample()
    {
        $this->markTestSkipped('This is a skip description');
    }

    public function testIncompleteExample()
    {
        $this->markTestIncomplete('This is a incomplete description');
    }

    public function testRiskyExample()
    {
        $this->markAsRisky();
    }

    public function testWarnExample()
    {
        $this->addWarning('This is a warning description');
    }

    public function testPassExample()
    {
        static::assertTrue(true);
    }
}
