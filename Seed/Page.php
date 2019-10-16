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
     * Page constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit(5);
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle() : \XF\Phrase
    {
        return $this->app->getContentTypePhrase('page', true);
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