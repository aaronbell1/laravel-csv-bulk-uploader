<?php

namespace Aaronbell1\LaravelCsvBulkUploader;

use Aaronbell1\LaravelCsvBulkUploader\Commands\MakeUploader;
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
        $this->registerCommands();
    }

    private function registerCommands()
    {
        if ($this->app->runningInConsole()) {
            $this->commands([
                MakeUploader::class
            ]);
        }
    }
}
