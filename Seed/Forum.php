<?php

namespace TickTackk\Seeder\Seed;

use XF\Phrase;

/**
 * Class Forum
 *
 * @package TickTackk\Seeder\Seed
 */
class Forum extends AbstractNode
{
    /**
     * @return string
     */
    protected function getNodeTypeId(): string
    {
        return 'Forum';
    }

    /**
     * @return null|\XF\Entity\AbstractNode
     */
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