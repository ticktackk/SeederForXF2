<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\AbstractNode as AbstractNodeEntity;

class Page extends AbstractNode
{
    protected function getNodeTypeId(): string
    {
        return 'Page';
    }

    protected function getNodeInput() : array
    {
        $nodeInput = parent::getNodeInput();

        $nodeInput['node_name'] = $this->faker()->slug();

        return $nodeInput;
    }

    protected function getRandomParentNode() :? AbstractNodeEntity
    {
        $faker = $this->faker();
        if (!$faker->boolean)
        {
            return null;
        }

        if ($faker->boolean)
        {
            return $this->finderWithRandomOrder('XF:Forum')->fetchOne();
        }

        return $this->finderWithRandomOrder('XF:Category')->fetchOne();
    }
}