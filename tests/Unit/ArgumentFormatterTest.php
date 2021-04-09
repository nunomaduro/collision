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
    public function itFormatsALongString(): void
    {
        $argumentFormatter = new ArgumentFormatter();

        $args = ['string' => 'Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque volutpat, enim ut ultrices efficitur, sapien justo viverra tellus, a auctor lacus risus quis neque. Proin dictum tincidunt placerat. Suspendisse vehicula arcu elit, a lobortis sem luctus sed. Nullam vehicula, leo sit amet malesuada imperdiet, felis orci tempus risus, non tincidunt lorem massa id ipsum. Nulla sem justo, feugiat et egestas eu, posuere ut dui. Cras quis bibendum justo. Cras finibus consequat mattis. Vivamus eu pretium odio. Suspendisse quis lacus molestie, tempus neque a, sagittis nunc. Etiam posuere quam sed metus volutpat facilisis. Maecenas vel dolor in neque maximus eleifend at in turpis. Nullam a tellus eget tortor volutpat ultricies aliquam sit amet felis. Phasellus efficitur massa consectetur, pharetra lacus eu, ultricies nunc. In sed sapien dignissim, convallis diam id, condimentum elit. Aenean feugiat euismod arcu, et mollis lacus vehicula eget. Aenean bibendum varius lorem vitae efficitur. Duis eget vel.'];

        $result = $argumentFormatter->format($args);

        $this->assertEquals($result, '"Lorem ipsum dolor sit amet, consectetur adipiscing elit. Quisque volutpat, enim ut ultrices efficitur, sapien justo viverra tellus, a auctor lacus risus quis neque. Proin dictum tincidunt placerat. Suspendisse vehicula arcu elit, a lobortis sem luctus sed. Nullam vehicula, leo sit amet malesuada imperdiet, felis orci tempus risus, non tincidunt lorem massa id ipsum. Nulla sem justo, feugiat et egestas eu, posuere ut dui. Cras quis bibendum justo. Cras finibus consequat mattis. Vivamus eu pretium odio. Suspendisse quis lacus molestie, tempus neque a, sagittis nunc. Etiam posuere quam sed metus volutpat facilisis. Maecenas vel dolor in neque maximus eleifend at in turpis. Nullam a tellus eget tortor volutpat ultricies aliquam sit amet felis. Phasellus efficitur massa consectetur, pharetra lacus eu, ultricies nunc. In sed sapien dignissim, convallis diam id, condimentum elit. Aenean feugiat euismod arcu, et mollis lacus vehicula eget. Aenean bibendum varius lorem vitae efficitur. Duis ege..."');
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
