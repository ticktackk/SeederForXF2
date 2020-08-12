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
        $randomNode = null;

        if ($faker->boolean)
        {
            if ($faker->boolean)
            {
                /** @var \XF\Entity\Forum $randomNode */
                $randomNode = $this->randomEntity('XF:Forum');
            }
            else
            {
                /** @var \XF\Entity\Forum $randomNode */
                $randomNode = $this->randomEntity('XF:Category');
            }
        }

        return $randomNode;
    }
}