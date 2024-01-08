<?php

declare(strict_types=1);

namespace Tests\Unit;

use NunoMaduro\Collision\Exceptions\TestException;
use PHPUnit\Event\Code\Throwable;
use PHPUnit\Framework\Attributes\Test;
use PHPUnit\Framework\TestCase;

class TestExceptionTest extends TestCase
{
    #[Test]
    public function itShortensContainsErrorMessages(): void
    {
        $message = <<<'EOF'
  Failed asserting that 'aaa
bbb
ccc
ddd
eee
fff' [UTF-8](length: 10248) does not contain "Pest" [UTF-8](length: 4).
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

    #[Test]
    public function itShortensNotContainErrorMessages(): void
    {
        $message = <<<'EOF'
  Failed asserting that 'aaa
bbb
ccc
ddd
eee
fff' [UTF-8](length: 10248) contains "Pest" [UTF-8](length: 4).
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
