<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class Page
 *
 * @package TickTackk\Seeder\Seed
 */
class Page extends AbstractNode
{
    /**
     * @return int
     */
    public function getRunOrder(): int
    {
        return 20;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return 5;
    }

    /**
     * @return string
     */
    protected function getNodeTypeId(): string
    {
        return 'Page';
    }

    /**
     * @return null|\XF\Entity\AbstractNode
     */
    protected function getRandomParentNode(): ?\XF\Entity\AbstractNode
    {
        if ($this->faker()->boolean)
        {
            /** @var \XF\Entity\Forum $randomPage */
            $randomPage = $this->randomEntity('XF:Forum');
        }
        else
        {
            /** @var \XF\Entity\Category $randomPage */
            $randomPage = $this->randomEntity('XF:Category');
        }

        return $randomPage;
    }
}