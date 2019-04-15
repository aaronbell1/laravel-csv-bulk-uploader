<?php

namespace Aaronbell1\LaravelCsvBulkUploader;

use Aaronbell1\LaravelCsvBulkUploader\Commands\MakeUploaderCommand;
use Illuminate\Support\ServiceProvider;

class LaravelCsvBulkUploaderServiceProvider extends ServiceProvider
{
    /**
     * Register services.
     *
     * @return void
     */
    public function register()
    {
        //
    }

    /**
     * Bootstrap services.
     *
     * @return void
     */
    public function boot()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeUploaderCommand::class
            ]);
        }
    }
}
