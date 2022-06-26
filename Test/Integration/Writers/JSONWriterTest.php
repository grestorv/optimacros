<?php

namespace Optimacros\Test\Integration;

include_once __DIR__ . '/../../../vendor/autoload.php';

use Optimacros\Readers\CSVReader;
use Optimacros\TreeGenerator\GenerateTrees;
use Optimacros\TreeGenerator\RelateSearcher;
use Optimacros\Writers\JSONWriter;

use PHPUnit\Framework\TestCase;

class JSONWriterTest extends TestCase
{
    const OUTPUTFILE = __DIR__ . '/output.json';
    const EXPECTEDOUTPUT = __DIR__ . '/expected_output.json';

    /**
     * @var JSONWriter
     */
    private $writer;
    private $treesLikeArray;

    /**
     * Почти полностью эмулируем оригинальный index.php, за исключением записи в файл
     */
    protected function setUp(): void
    {
        $reader = new CSVReader(__DIR__ . '/input.csv');
        $reader->read();
        $generate = new GenerateTrees();
        $generate->setRelateSearcher(new RelateSearcher());
        $generate->setIterator($reader->getRecords());
        $generate->run();
        $this->treesLikeArray = $generate->getTreesLikeArray();
        $this->writer = new JSONWriter(self::OUTPUTFILE);
    }

    /** Сравнивает файл, сгенерированный исправным кодом, с файлом генерируемым сейчас
     * @throws \Exception
     */
    public function testWrite(): void
    {
        if(file_exists(self::OUTPUTFILE))
            unlink(self::OUTPUTFILE);
        $this->writer->write($this->treesLikeArray);
        $this->assertFileEquals(self::EXPECTEDOUTPUT, self::OUTPUTFILE);
    }
}
