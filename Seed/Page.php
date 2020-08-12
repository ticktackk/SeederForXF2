<?php

namespace TickTackk\Seeder\Seed;

use XF\Phrase;

/**
 * Class Page
 *
 * @package TickTackk\Seeder\Seed
 */
class Page extends AbstractNode
{
    /**
     * @return string
     */
    protected function getNodeTypeId(): string
    {
        return 'Page';
    }

    /**
     * @return array
     */
    protected function getNodeInput() : array
    {
        $nodeInput = parent::getNodeInput();

        $nodeInput['node_name'] = $this->faker()->slug();

        return $nodeInput;
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