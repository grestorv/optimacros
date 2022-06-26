<?php

namespace Optimacros\Test\TreeGenerator\Unit;

use Optimacros\TreeGenerator\Node;
use PHPUnit\Framework\TestCase;


class NodeTest extends TestCase
{
    /**
     * @var Node
     */
    private $node;
    /**
     * @var Node
     */
    private $parentNode;

    /**
     * Задает начальные условия для каждого теста
     * @throws \ReflectionException
     */
    protected function setUp(): void
    {
        $connection = $this->createMock(\PDO::class);
        $this->parentNode = new Node(
            [
                'Item Name' => 'Total',
                'Type' => "Изделия и компоненты",
                "Parent" => "",
                "Relation" => ""
            ]
        );
        $this->node = new Node(
            [
                'Item Name' => 'ПВЛ',
                'Type' => "Изделия и компоненты",
                "Parent" => "Total",
                "Relation" => "Relation1"
            ],
            $this->parentNode
        );
    }

    /**
     * @throws \Exception
     */
    public function testGetName(): void
    {
        $name = $this->node->getName();
        $this->assertEquals('ПВЛ', $name);
    }

    /**
     * @throws \Exception
     */
    public function testGetRelation(): void
    {
        $relation = $this->node->getRelation();
        $this->assertEquals("Relation1", $relation);
    }

    /**
     * @throws \Exception
     */
    public function testGetParentName(): void
    {
        $parentName = $this->node->getParentName();
        $this->assertEquals('Total', $parentName);
    }

    /**
     * @throws \Exception
     */
    public function testGetChildren(): void
    {
        $this->parentNode->addChild($this->node);
        $children = $this->parentNode->getChildren();
        $this->assertEquals($children, [$this->node]);
    }

    /**
     * @throws \Exception
     */
    public function testAddChild(): void
    {
        $children = $this->parentNode->getChildren();
        $this->assertEquals([], $children);
        $this->parentNode->addChild($this->node);
        $children = $this->parentNode->getChildren();
        $this->assertEquals($children, [$this->node]);
    }

    /**
     * Проверяет работу клонирования. При клонировании ноды и их дети должны совпадать по аттрибутам,
     * но должны быть разными объектами
     * @throws \Exception
     */
    public function testClone(): void
    {
        $this->parentNode->addChild($this->node);
        $parentNodeClone = clone $this->parentNode;
        $this->assertEquals($this->parentNode, $parentNodeClone);
        $this->assertNotSame($this->parentNode, $parentNodeClone);
        $this->assertEquals($this->parentNode->getChildren(), $parentNodeClone->getChildren());
        $this->assertNotSame($this->parentNode->getChildren(), $parentNodeClone->getChildren());
    }

    /**
     * @throws \Exception
     */
    public function testGetParentNode(): void
    {
        $this->assertSame($this->parentNode, $this->node->getParentNode());
    }

    /**
     * @throws \Exception
     */
    public function testSetParentNode(): void
    {
        $newParentNode = new Node(
            [
                'Item Name' => 'Total2',
                'Type' => 'Изделия и компоненты',
                'Parent' => '',
                'Relation' => ''
            ]
        );
        $this->node->setParentNode($newParentNode);
        $this->assertSame($newParentNode, $this->node->getParentNode());
    }

    /**
     * Создает три новых ноды. Одну из них присоединяет к родительской, две другие к дочерней.
     * Проверяет что результирующий массив совпадает с тем что должен быть
     * @throws \Exception
     */
    public function testGetAllChildren(): void
    {
        $childNodeArray1 = [
            'Item Name' => '1',
            'Type' => 'Изделия и компоненты',
            'Parent' => 'Total',
            'Relation' => ''
        ];
        $childNodeArray2 = [
            'Item Name' => '2',
            'Type' => 'Изделия и компоненты',
            'Parent' => 'ПВЛ',
            'Relation' => ''
        ];
        $childNodeArray3 = [
            'Item Name' => '3',
            'Type' => 'Изделия и компоненты',
            'Parent' => 'ПВЛ',
            'Relation' => ''
        ];

        $this->parentNode->addChild($this->node);
        $childNode1 = new Node($childNodeArray1, $this->parentNode);
        $this->parentNode->addChild($childNode1);
        $childNode2 = new Node($childNodeArray2, $this->node);
        $this->node->addChild($childNode2);
        $childNode3 = new Node($childNodeArray3, $this->node);
        $this->node->addChild($childNode3);
        $childNodeArray = [$this->node, $childNode1, $childNode2, $childNode3,];


        $this->assertEquals($childNodeArray, $this->parentNode->getAllChildren());
    }
}
