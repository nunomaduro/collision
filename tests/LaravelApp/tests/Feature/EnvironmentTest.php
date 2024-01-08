<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use PHPUnit\Framework\Attributes\Test;
use Tests\TestCase;

#[Group('environment')]
class EnvironmentTest extends TestCase
{
    #[Test]
    public function variableOnlyInDotEnv()
    {
        $this->assertEquals('VAL_IN_DOT_ENV', env('VAR_IN_DOT_ENV'));
        $this->assertEquals(null, env('VAR_IN_DOT_ENV_TESTING'));
    }

    #[Test]
    public function variableOnlyInPhpunit()
    {
        $this->assertEquals('VAL_IN_PHPUNIT', env('VAR_IN_PHPUNIT'));
    }

    #[Test]
    public function variableInDotEnvButOverriddenInPhpunit()
    {
        $this->assertEquals('VAL_OVERRIDDEN_IN_PHPUNIT', env('VAR_OVERRIDDEN_IN_PHPUNIT'));
    }
}
