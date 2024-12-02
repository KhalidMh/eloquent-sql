<?php

namespace KhalidMh\EloquentSQL\Tests;

use Orchestra\Testbench\TestCase as OrchestraTestCase;

abstract class TestCase extends OrchestraTestCase
{
    protected function setUp(): void
    {
        parent::setUp();

        // load the package migrations
        $this->loadMigrationsFrom(__DIR__ . '/../database/migrations');

        // run the migrations
        $this->artisan('migrate');
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
