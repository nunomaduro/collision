<?php

namespace Tests\Feature;

use PHPUnit\Framework\TestCase;

class WritesExceptionDetailsTest extends TestCase
{
    use FakeFailTrait;

    /** @test */
    public function it_renders_the_title(): void
    {
        $result = $this->fakeFail();

        $this->assertContains('Tests\Feature\FakeProgram\FakeException  : Fail description', $result);
    }

    /** @test */
    public function it_renders_the_editor(): void
    {
        $result = $this->fakeFail();

        $dir = __DIR__;

        $expectedResult = <<<EOF
  at $dir/FakeProgram/HelloWorldFile3.php: 9
  5: class HelloWorldFile3
  6: {
  7:     public static function say()
  8:     {
  9:         throw new FakeException('Fail description');
  10:     }
  11: }
EOF;

        $this->assertContains($expectedResult, $result);
    }

    /** @test */
    public function it_writes_the_trace_without_details(): void
    {
        $result = $this->fakeFail(false);
        $dir = __DIR__;

        $expectedResult = <<<EOF
  Exception trace:

  1   Tests\Feature\FakeProgram\HelloWorldFile3::say()
      $dir/FakeProgram/HelloWorldFile2.php : 9

  2   Tests\Feature\FakeProgram\HelloWorldFile2::say()
      $dir/FakeProgram/HelloWorldFile1.php : 9

  Please use the argument -v to see more details.
EOF;

        $this->assertContains($expectedResult, $result);
    }
}
