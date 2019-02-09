<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class Forum
 *
 * @package TickTackk\Seeder\Seed
 */
class Forum extends AbstractNode
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
        return 50;
    }

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
        /** @var \XF\Entity\Forum $randomForum */
        $randomForum = $this->randomEntity('XF:Forum');
        return $randomForum;
    }
}