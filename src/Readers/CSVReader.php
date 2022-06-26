<?php

namespace Optimacros\Readers;

use League\Csv\Reader;

class CSVReader
{
    /**
     * @var \Traversable
     */
    private $records;
    /**
     * @var array|string[]
     */
    private $header;
    private $path;

    public function __construct($path)
    {
        $this->path = $path;
    }

    /**
     * Создает итератор для заданного файла, не считывая его полностью
     * @throws \League\Csv\Exception
     * @throws \League\Csv\InvalidArgument
     */
    public function read()
    {
        $csv = Reader::createFromPath($this->path, 'r');
        $csv->setHeaderOffset(0);
        $csv->setDelimiter(';');

        $this->header = $csv->getHeader();
        $this->records = $csv->getRecords();
    }

    /**
     * Возвращает итератор всех записей
     * @return \Traversable
     */
    public function getRecords()
    {
        return $this->records;
    }
}
