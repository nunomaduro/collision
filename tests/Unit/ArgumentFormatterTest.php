<?php

namespace Tests\Unit;

use PHPUnit\Framework\TestCase;
use NunoMaduro\Collision\ArgumentFormatter;
use NunoMaduro\Collision\Contracts\ArgumentFormatter as ArgumentFormatterContract;

class ArgumentFormatterTest extends TestCase
{
    /** @test */
    public function it_respects_is_contract(): void
    {
        $this->assertInstanceOf(ArgumentFormatterContract::class, new ArgumentFormatter());
    }

    /** @test */
    public function it_formats_a_string(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $args = ['string' => 'foo'];

        $result = $argumentFormatter->format($args);

        $this->assertEquals($result, '"foo"');
    }

    /** @test */
    public function it_formats_a_array(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $args = ['array' => ['foo' => 'bar', 'key' => 'value']];

        $result = $argumentFormatter->format($args);

        $this->assertEquals($result, '["bar", "value"]');
    }

    /** @test */
    public function it_formats_a_object(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $object = new \stdClass();

        $result = $argumentFormatter->format([$object]);

        $this->assertEquals($result, 'Object(stdClass)');
    }
}
