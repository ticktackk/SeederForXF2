<?php

namespace TickTackk\Seeder\Seed;

class Forum extends AbstractNode
{
    protected function getNodeTypeId(): string
    {
        return 'Forum';
    }

    protected function getRandomParentNode(): ?\XF\Entity\AbstractNode
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