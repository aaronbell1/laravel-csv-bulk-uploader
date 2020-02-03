<?php

namespace Aaronbell1\LaravelCsvBulkUploader;

use Aaronbell1\LaravelCsvImporter\CsvLoader;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Validator;

abstract class BulkUploader
{
    protected const DATE_FORMAT = 'd/m/y';

    protected $loader;
    protected $validator;
    protected $invalidRows;

    /**
     * BulkUploader constructor.
     */
    public function __construct()
    {
        $this->loader = new CsvLoader;
    }

    /**
     * @return array
     */
    abstract protected function rules();

    /**
     * @return array
     */
    abstract protected function messages();

    /**
     * @param $row
     * @return void
     */
    abstract protected function saveRow($row);

    protected function before()
    {
        return;
    }

    /**
     * @throws \Exception
     */
    public function save()
    {
        DB::beginTransaction();
        try {
            $this->before();

            foreach ($this->loader->data as $row) {
                $this->saveRow($row);
            }
        } catch (\Exception $e) {
            DB::rollBack();

            throw $e;
        }
        DB::commit();
    }

    /**
     * @param $filePath
     * @throws \Aaronbell1\LaravelCsvImporter\Exceptions\HeaderRowUnmatchedException
     */
    public function load($filePath)
    {
        $this->loader->load($filePath);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->validate();
    }

    /**
     * @return bool
     */
    private function validate()
    {
        $this->createValidator($this->loader->data);

        if ($this->validator->fails()) {
            $this->getInvalidRows();
        }

        return !$this->validator->fails();
    }

    /**
     * @param $data
     */
    private function createValidator($data)
    {
        $this->validator = Validator::make($data, $this->rulesAsArray(), $this->messagesAsArray());
    }

    /**
     * @return array
     */
    private function rulesAsArray()
    {
        return $this->prefixKeys($this->rules());
    }

    /**
     * @return array
     */
    private function messagesAsArray()
    {
        return $this->prefixKeys($this->messages());
    }

    /**
     * @return void
     */
    private function getInvalidRows()
    {
        $errorsKeys = collect(array_keys($this->validator->errors()->messages()))
            ->map(function ($error) {
                return $error[0];
            })->unique()->toArray();

        $this->invalidRows = $this->loader->getRowsByIndexes($errorsKeys);
    }

    /**
     * @param string $dataName
     * @return \Illuminate\Http\RedirectResponse
     */
    public function redirectWithErrors($dataName = 'data')
    {
        return redirect()->back()
            ->with([$dataName => $this->invalidRows])
            ->withErrors($this->validator);
    }

    /**
     * @param $str
     * @return bool|string
     */
    public function str_to_utf8($str)
    {

        if (mb_detect_encoding($str, 'UTF-8', true) === false) {
            $str = utf8_encode($str);
        }

        return $str;
    }

    /**
     * @param $array
     * @param string $prefix
     * @return array
     */
    private function prefixKeys($array, $prefix = '*.')
    {
        return array_combine(
            array_map(function($key)use($prefix){ return $prefix.$key; }, array_keys($array)),
            $array
        );
    }
}
