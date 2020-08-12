<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class Category
 *
 * @package TickTackk\Seeder\Seed
 */
class Category extends AbstractNode
{
    /**
     * @return string
     */
    protected function getNodeTypeId(): string
    {
        return 'Category';
    }

    /**
     * @return null|\XF\Entity\AbstractNode
     */
    protected function getRandomParentNode(): ?\XF\Entity\AbstractNode
    {
        /** @var \XF\Entity\Category $randomCategory */
        $randomCategory = $this->randomEntity('XF:Category');
        return $randomCategory;
    }

    /**
     * @return array
     */
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