<?php

declare(strict_types=1);

namespace Tests\Feature;

use Tests\TestCase;

/**
 * @group environmentCustomVariables
 */
class EnvironmentCustomVariablesTest extends TestCase
{
    /**
     * @group environmentNoCVPhpunit
     */
    public function testEnvironmentNoCustomVariablesPhpunit()
    {
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentNoCVParallel
     */
    public function testEnvironmentNoCustomVariablesParallel()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentNoCVParallelRecreate
     */
    public function testEnvironmentNoCustomVariablesParallelWithRecreate()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentNoCVParallelDrop
     */
    public function testEnvironmentNoCustomVariablesParallelWithDrop()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentCVPhpunit
     */
    public function testEnvironmentCustomVariablesPhpunit()
    {
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentCVParallel
     */
    public function testEnvironmentCustomVariablesParallel()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentCVParallelRecreate
     */
    public function testEnvironmentCustomVariablesParallelWithRecreate()
    {
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING'));
        $this->assertEquals(1, env('LARAVEL_PARALLEL_TESTING_RECREATE_DATABASES'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE'));
        $this->assertEquals(null, env('LARAVEL_PARALLEL_TESTING_DROP_DATABASES'));
        $this->assertEquals(null, env('CUSTOM_ENV_VARIABLE_FOR_PHPUNIT'));
        $this->assertEquals(1, env('CUSTOM_ENV_VARIABLE_FOR_PARALLEL'));
    }

    /**
     * @group environmentCVParallelDrop
     */
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
