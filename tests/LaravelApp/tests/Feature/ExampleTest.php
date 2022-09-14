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
        // ..
    }

    public function testDeprecationExample()
    {
        trigger_deprecation('foo', '1.0', 'This is a deprecation description');

        $this->assertTrue(true);
    }

    public function testPassExample()
    {
        static::assertTrue(true);
    }
}
