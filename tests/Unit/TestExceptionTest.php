<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\Exceptions\TestException;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\TestCase;

class TestExceptionTest extends TestCase
{
    /** @test */
    public function itShortensContainsErrorMessages(): void
    {
        $message = <<<'EOF'
  Failed asserting that 'aaa
bbb
ccc
ddd
eee
fff' does not contain "Pest".
EOF;

        $expect = <<<'EOF'
Expected: aaa
  bbb
  ccc
  ... (3 more lines)

  Not to contain: Pest
EOF;

        $testException = new TestException(new Throwable(self::class, $message, 'description', '', null), false);

        $this->assertStringContainsString($expect, strip_tags($testException->getMessage()));
    }

    /** @test */
    public function itShortensNotContainErrorMessages(): void
    {
        $message = <<<'EOF'
  Failed asserting that 'aaa
bbb
ccc
ddd
eee
fff' contains "Pest".
EOF;

        $expect = <<<'EOF'
Expected: aaa
  bbb
  ccc
  ... (3 more lines)

  To contain: Pest
EOF;

        $testException = new TestException(new Throwable(self::class, $message, 'description', '', null), false);

        $this->assertStringContainsString($expect, strip_tags($testException->getMessage()));
    }
}
