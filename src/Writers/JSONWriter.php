<?php

namespace Optimacros\Writers;

class JSONWriter
{
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Пишет переданный массив в json-файл
     * @param $array
     */
    public function write($array)
    {
        $json = json_encode($array, JSON_PRETTY_PRINT | JSON_UNESCAPED_UNICODE);
        file_put_contents($this->path, $json);
    }
}
