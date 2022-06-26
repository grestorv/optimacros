<?php

namespace Optimacros\TreeGenerator;

/**
 * Класс занимается поиском relation-связей, добавляет ноды в деревья
 * Основной метод search() вызывается после первоначальной генерации всех деревьев через parent-связь
 * Class RelateSearcher
 * @package Optimacros\TreeGenerator
 */
class RelateSearcher
{
    /**
     * @var array
     */
    private $relationedNodes;
    private $trees;

    public function __construct()
    {
        $this->relationedNodes = [];
    }

    /**
     * Добавляет ноду в список нод, имеющих relation-связь, где ключом списка является relation-родитель ноды
     * Если нода сама является relation-родителем, и при этом relation-потомком, то записываем ее родителя в отдельную переменную
     * @param Node $node
     */
    public function addNode(Node $node)
    {
        if (!empty($node->getRelation())) {
            $this->relationedNodes[$node->getRelation()]['nodes'][] = $node;
            if ($this->relationedNodes[$node->getName()]) {
                $this->relationedNodes[$node->getName()]['parentName'] = $node->getRelation();
            }
        }
    }

    /**
     * Проходится по всем relation-нодам, и подставляет их в дерево
     */
    public function search()
    {
        foreach ($this->relationedNodes as $relationedNodeName => $relationedNode) {
            $this->addRelation($relationedNodeName);
        }
    }

    /**
     * Устанавливает деревья, с которыми будет работать объект
     * @param $trees
     */
    public function setTrees($trees)
    {
        $this->trees = $trees;
    }

    /**
     * Функция добавляющая relation-связи в деревья
     * @param $nodeName
     */
    private function addRelation($nodeName)
    {
        $nodes = $this->relationedNodes[$nodeName]['nodes'];
        if (isset($this->relationedNodes[$nodeName]['parentName'])) {
            // если у ноды есть relation-родитель, то сначала сформируем его
            $this->addRelation($this->relationedNodes[$nodeName]['parentName']);
        }

        // ищем ноду по всем деревьям
        foreach ($this->trees as $tree) {
            $relationParent = $tree->searchNode($nodeName);
            if ($relationParent) {
                break;
            }
        }

        // находим всех потомков найденной ноды, проходимся по ним, и добавляем их текущей ноде со сменой родителя
        // Добавляем всех их потомков в список relation-нод, чтобы пройтись по ним позже
        $relationChildren = $relationParent->getChildren();
        foreach ($nodes as $node) {
            foreach ($relationChildren as $child) {
                $child = clone $child;
                $child->setParentNode($node);
                $node->addChild($child);

                $this->addNode($child);
                foreach ($child->getAllChildren() as $newChild) {
                    $this->addNode($newChild);
                }
            }
        }

        unset($this->relationedNodes[$nodeName]);
    }
}
