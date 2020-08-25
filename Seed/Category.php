<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\AbstractNode as AbstractNodeEntity;

class Category extends AbstractNode
{
    protected function getNodeTypeId(): string
    {
        return 'Category';
    }

    protected function getRandomParentNode() :? AbstractNodeEntity
    {
        return $this->finderWithRandomOrder('XF:Category')->fetchOne();
    }

    protected function getNodeInput() : array
    {
        $nodeInput = parent::getNodeInput();

        if ($this->faker()->boolean)
        {
            $nodeInput['parent_node_id'] = 0;
        }

        return $nodeInput;
    }
}