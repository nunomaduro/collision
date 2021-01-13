<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\ArgumentFormatter;
use NunoMaduro\Collision\Contracts\ArgumentFormatter as ArgumentFormatterContract;
use PHPUnit\Framework\TestCase;

class ArgumentFormatterTest extends TestCase
{
    /** @test */
    public function itRespectsIsContract(): void
    {
        $this->assertInstanceOf(ArgumentFormatterContract::class, new ArgumentFormatter());
    }

    /** @test */
    public function itFormatsAString(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $args = ['string' => 'foo'];

        $result = $argumentFormatter->format($args);

        $this->assertEquals($result, '"foo"');
    }

    /** @test */
    public function itFormatsAArray(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $args = ['array' => ['foo' => 'bar', 'key' => 'value']];

        $result = $argumentFormatter->format($args);

        $this->assertEquals($result, '["bar", "value"]');
    }

    /** @test */
    public function itFormatsAObject(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $object = new \stdClass();

        $result = $argumentFormatter->format([$object]);

        $this->assertEquals($result, 'Object(stdClass)');
    }
}
