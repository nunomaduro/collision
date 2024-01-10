<?php

declare(strict_types=1);

namespace Tests\Feature;

use PHPUnit\Framework\Attributes\Group;
use Tests\TestCase;

#[Group('environmentCustomVariables')]
class EnvironmentCustomVariablesTest extends TestCase
{
    #[Group('environmentNoCVPhpunit')]
    public function testEnvironmentNoCustomVariablesPhpunit()
    {
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentNoCVParallel')]
    public function testEnvironmentNoCustomVariablesParallel()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentNoCVParallelRecreate')]
    public function testEnvironmentNoCustomVariablesParallelWithRecreate()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentNoCVParallelDrop')]
    public function testEnvironmentNoCustomVariablesParallelWithDrop()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentCVPhpunit')]
    public function testEnvironmentCustomVariablesPhpunit()
    {
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentCVParallel')]
    public function testEnvironmentCustomVariablesParallel()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentCVParallelRecreate')]
    public function testEnvironmentCustomVariablesParallelWithRecreate()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    #[Group('environmentCVParallelDrop')]
    public function testEnvironmentCustomVariablesParallelWithDrop()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }
}
