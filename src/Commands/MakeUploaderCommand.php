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
     * The subdirectory that the file will be created within
     *
     * @var string
     */
    protected $subDirectory = 'Uploaders';

    /**
     * Get the stub file for the generator.
     *
     * @return string
     */
    protected function getStub()
    {
        return __DIR__.'/../stubs/uploader.stub';
    }

    /**
     * Get the destination class path.
     *
     * @param  string  $name
     * @return string
     */
    protected function getPath($name)
    {
        $name = Str::replaceFirst($this->rootNamespace(), '', $name);

        return $this->laravel['path'].'/'."$this->subDirectory/".str_replace('\\', '/', $name).'.php';
    }
}
