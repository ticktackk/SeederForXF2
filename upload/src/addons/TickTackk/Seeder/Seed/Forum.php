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
     * Forum constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit(25);
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle() : \XF\Phrase
    {
        return $this->app->getContentTypePhrase('forum', true);
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