<?php

namespace Aaronbell1\LaravelCsvBulkUploader\Commands;

use Illuminate\Console\GeneratorCommand;

class MakeUploader extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:uploader';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new uploader class to bulk upload a CSV file.';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'../stubs/model.stub';
    }
}
