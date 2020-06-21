<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @group environment
 */
class EnvironmentTest extends TestCase
{
    /** @test */
    public function variable_only_in_dot_env()
    {
        $this->assertEquals('VAL_IN_DOT_ENV', env('VAR_IN_DOT_ENV'));
        $this->assertEquals(null, env('VAR_IN_DOT_ENV_TESTING'));
    }

    /** @test */
    public function variable_only_in_phpunit()
    {
        $this->assertEquals('VAL_IN_PHPUNIT', env('VAR_IN_PHPUNIT'));
    }

    /** @test */
    public function variable_in_dot_env_but_overridden_in_phpunit()
    {
        $this->assertEquals('VAL_OVERRIDDEN_IN_PHPUNIT', env('VAR_OVERRIDDEN_IN_PHPUNIT'));
    }
}
