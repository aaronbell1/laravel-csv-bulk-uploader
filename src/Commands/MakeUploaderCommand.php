<?php

namespace Aaronbell1\LaravelCsvBulkUploader\Commands;

use Illuminate\Console\GeneratorCommand;
use Illuminate\Support\Str;

class MakeUploaderCommand extends GeneratorCommand
{
    /**
     * The name and signature of the console command.
     *
     * @var string
     */
    protected $signature = 'make:uploader {name}';

    /**
     * The console command description.
     *
     * @var string
     */
    protected $description = 'Create a new uploader class to bulk upload a CSV file.';

    /**
     * The type of class being generated.
     *
     * @var string
     */
    protected $type = 'Bulk uploader';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/uploader.stub';
    }
}
