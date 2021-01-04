<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @group environmentTesting
 */
class EnvironmentTestingTest extends TestCase
{
    /** @test */
    public function variableOnlyInDotEnv()
    {
        $this->assertEquals(null, env('VAR_IN_DOT_ENV'));
        $this->assertEquals('VAL_IN_DOT_ENV_TESTING', env('VAR_IN_DOT_ENV_TESTING'));
    }

    /** @test */
    public function variableOnlyInPhpunit()
    {
        $this->assertEquals('VAL_IN_PHPUNIT', env('VAR_IN_PHPUNIT'));
    }

    /** @test */
    public function variableInDotEnvButOverriddenInPhpunit()
    {
        $this->assertEquals('VAL_OVERRIDDEN_IN_PHPUNIT', env('VAR_OVERRIDDEN_IN_PHPUNIT'));
    }
}
