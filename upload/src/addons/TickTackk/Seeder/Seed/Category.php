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
     * @return int
     */
    public function getRunOrder(): int
    {
        return 10;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return 20;
    }

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
    protected function getNodeInput()
    {
        $nodeInput = parent::getNodeInput();

        if ($this->faker()->boolean)
        {
            $nodeInput['parent_node_id'] = 0;
        }

        return $nodeInput;
    }
}