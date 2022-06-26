<?php

namespace Optimacros\TreeGenerator;

/**
 * Класс дерева, отвечает за создание, хранение и поиск по нодам
 * Class Tree
 * @package Optimacros\TreeGenerator
 */
class Tree
{
    /**
     * @var Node
     */
    private $root;

    /**
     * @var array
     */
    private $relates;
    /**
     * @var RelateSearcher
     */
    private $relateSearcher;

    public function __construct(\Traversable $iterator, RelateSearcher $relateSearcher)
    {
        $this->root = new Node($iterator->current());// первая нода всегда является корневой, записываем ее в отдельную переменную
        $iterator->next();
        $this->relateSearcher = $relateSearcher;
        while ($iterator->valid() && $iterator->current()['Parent']) {
            $this->relates[$iterator->current()['Parent']][] = $iterator->current();// записываем соответствие нод и их предков
            $iterator->next();
        }
        $this->buildTree($this->root);
        $this->relateSearcher->addNode($this->root);
    }

    /** Строит дерево. Для каждой ноды ищет потомков в хэш-таблице соответствий, и создает для каждого потомка ноду.
     * После чего рекурсивно вызывается для каждого потомка
     * @param Node $parentNode
     */
    private function buildTree(Node $parentNode)
    {
        $children = $this->relates[$parentNode->getName()];
        if (!$children) {
            return;
        }
        foreach ($children as $child) {
            $childNode = new Node($child, $parentNode);
            $parentNode->addChild($childNode);
            $this->relateSearcher->addNode($childNode);
            $this->buildTree($childNode);
        }
    }

    /**
     * Возвращает дерево как многомерный массив в конкретном формате
     * @return array
     */
    public function getTreeLikeArray()
    {
        return $this->getNodeLikeArray($this->root);
    }

    public function getNodeLikeArray(Node $node)
    {
        $newData = [
            'itemName' => $node->getName(),
            'parent' => $node->getParentName(),
            'children' => []
        ];
        foreach ($node->getChildren() as $child) {
            $newData['children'][] = $this->getNodeLikeArray($child);
        }
        return $newData;
    }

    /**
     * Ищет ноду
     * @param $name
     * @return Node|null
     */
    public function searchNode($name)
    {
        return $this->recursiveSearch($this->root, $name);
    }

    /**
     * Проверяет, является ли текущая нода искомой, и если нет, то рекурсивно вызывается для всех ее потомков
     * @param Node $node
     * @param string $name
     * @return Node|null
     */
    public function recursiveSearch(Node $node, string $name)
    {
        if ($name === $node->getName()) {
            return $node;
        }
        foreach ($node->getChildren() as $child) {
            $node = $this->recursiveSearch($child, $name);
            if ($node) {
                return $node;
            }
        }
        return null;
    }
}
