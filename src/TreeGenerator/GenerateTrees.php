<?php

namespace Optimacros\TreeGenerator;

/**
 * Отвечает за создание и хранение деревьев
 * Class GenerateTrees
 * @package Optimacros\TreeGenerator
 */
class GenerateTrees
{
    /**
     * @var array
     */
    private $trees;
    /**
     * @var RelateSearcher
     */
    private $relateSearcher;
    /**
     * @var array
     */
    private $treeArray;
    private $iterator;

    public function __construct()
    {
        $this->trees = [];
        $this->relateSearcher = null;
    }

    public function setIterator(\Traversable $iterator)
    {
        $this->iterator = $iterator;
    }

    public function setRelateSearcher(RelateSearcher $relateSearcher)
    {
        $this->relateSearcher = $relateSearcher;
    }

    public function getTreesLikeArray()
    {
        return $this->treeArray;
    }

    public function run()
    {
        $this->iterator->rewind();
        while ($this->iterator->valid()) {
            $this->trees[] = new Tree($this->iterator, $this->relateSearcher);
        }

        $this->relateSearcher->setTrees($this->trees);
        $this->relateSearcher->search();

        foreach ($this->trees as $tree) {
            $this->treeArray[] = $tree->getTreeLikeArray();
        }
    }
}
