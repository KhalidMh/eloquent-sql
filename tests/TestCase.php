<?php

namespace KhalidMh\EloquentSQL\Tests;

// use PHPUnit\Framework\TestCase as BaseTestCase;
use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // load the package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');
    }

    protected function getPackageProviders($app)
    {
        return [
            'KhalidMh\EloquentSQL\EloquentSQLServiceProvider',
        ];
    }

    protected function getEnvironmentSetUp($app)
    {
        // perform environment setup
    }
}
