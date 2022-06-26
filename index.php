<?php
include_once __DIR__ . '/vendor/autoload.php';

use Optimacros\Readers\CSVReader;
use Optimacros\Writers\JSONWriter;
use Optimacros\TreeGenerator\GenerateTrees;
use Optimacros\TreeGenerator\RelateSearcher;

$reader = new CSVReader($argv[1]);// получаем все записи из файла
$reader->read();
$generate = new GenerateTrees();
$generate->setRelateSearcher(new RelateSearcher());
$generate->setIterator($reader->getRecords());// задаем записи генератору деревьев через итератор, чтобы он мог по ним пройтись
$generate->run();// запускаем генерацию деревьев
$writer = new JSONWriter($argv[2]);
$writer->write($generate->getTreesLikeArray());// получаем деревья как многомерный массив и пишем их в файл
