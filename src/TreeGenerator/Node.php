<?php

namespace Optimacros\TreeGenerator;

/**
 * Класс отдельного элемента списка, нода.
 * Class Node
 * @package Optimacros\TreeGenerator
 */
class Node
{

    /**
     * @var mixed
     */
    private $name;
    /**
     * @var mixed
     */
    private $type;
    /**
     * @var mixed
     */
    private $relation;
    /**
     * @var Node|null
     */
    private $parentNode;
    /**
     * @var array
     */
    private $children;

    public function __construct($data, Node &$parentNode = null)
    {
        $this->name = $data['Item Name'];
        $this->type = $data['Type'];
        $this->relation = $data['Relation'];
        $this->parentNode = $parentNode;
        $this->children = [];
    }

    /** Возвращает имя ноды
     * @return string
     */
    public function getName(): string
    {
        return $this->name;
    }

    /**
     * Возвращает relation ноды
     * @return mixed
     */
    public function getRelation()
    {
        return $this->relation;
    }

    /**
     * Возвращает имя родителя ноды
     * @return string|null
     */
    public function getParentName()
    {
        return $this->parentNode ? $this->parentNode->getName() : null;
    }

    /**
     * Возвращает родителя ноды
     * @return Node|null
     */
    public function getParentNode()
    {
        return $this->parentNode;
    }

    /**
     * Возвращает массив потомков ноды
     * @return array
     */
    public function getChildren(): array
    {
        return $this->children;
    }

    /**
     * Добавляет потомка ноде
     * @param Node $child
     */
    public function addChild(Node $child)
    {
        array_push($this->children, $child);
    }

    public function __clone()
    {
        // При клонировании важно также склонировать всех потомков.
        // Очевидно вызовется также и на их потомках, если таковые будут.
        foreach ($this->children as $key => $child) {
            $this->children[$key] = clone $child;
        }
    }

    /**
     * Меняет родителя ноды
     * @param Node $node
     */
    public function setParentNode(Node $node)
    {
        $this->parentNode = $node;
    }

    /**
     * Отдает всех потомков ноды в виде одномерного массива
     * @return array
     */
    public function getAllChildren()
    {
        $children = [];
        if (!empty($this->getChildren())) {
            foreach ($this->getChildren() as $child) {
                $children = $children + $child->getAllChildren();
            }
        }
        $return = array_merge($this->getChildren(), $children);
        return $return;
    }
}
