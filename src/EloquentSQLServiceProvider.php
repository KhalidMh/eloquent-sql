<?php

namespace KhalidMh\EloquentSQL;

use Illuminate\Support\ServiceProvider;

class EloquentSQLServiceProvider extends ServiceProvider
{
    public function register()
    {
        // Register any application services.
    }

    public function boot()
    {
        if (version_compare(app()->version(), '11.0.0', '<')) {
            ServiceProvider::addProviderToBootstrapFile(EloquentSQLServiceProvider::class);
        }
    }
}
