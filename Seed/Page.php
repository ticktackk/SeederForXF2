<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\AbstractNode as AbstractNodeEntity;
use XF\Entity\Node as NodeEntity;
use XF\Mvc\FormAction;
use XF\Entity\Page as PageEntity;

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

    /**
     * @param NodeEntity $node
     * @param AbstractNodeEntity|PageEntity $data
     * @param FormAction $formAction
     */
    protected function setupFormAction(
        NodeEntity $node,
        AbstractNodeEntity $data,
        FormAction $formAction
    ): void
    {
        $template = $data->getMasterTemplate();

        $faker = $this->faker();
        $template->template = $faker->boolean ? $faker->randomHtml() : $faker->text();

        $formAction->apply(function () use($template)
        {
            $template->save();
        });
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