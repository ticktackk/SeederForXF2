<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Entity\Node as NodeEntity;
use XF\Entity\Page as PageEntity;
use XF\Mvc\FormAction;
use XF\Entity\AbstractNode as AbstractNodeEntity;

abstract class AbstractNode extends AbstractSeed
{
    abstract protected function getNodeTypeId() : string;

    protected function getRandomParentNode() :? \XF\Entity\AbstractNode
    {
        return null;
    }

    protected function getNodeInput() : array
    {
        $faker = $this->faker();
        $parentNode = $this->getRandomParentNode();

        return [
            'parent_node_id' => $parentNode ? $parentNode->node_id : 0,
            'title' => implode(' ', Lorem::words()),
            'description' => $faker->paragraph,
            'display_order' => $faker->randomNumber(),
            'display_in_list' => true,
            'style_id' => 0,
            'navigation_id' => 'str',
        ];
    }

    /**
     * @since 1.1.0 Alpha 4
     *
     * @param NodeEntity $node
     * @param AbstractNodeEntity $data
     * @param FormAction $formAction
     */
    protected function setupFormAction(
        NodeEntity $node,
        AbstractNodeEntity $data,
        FormAction $formAction
    ) : void
    {
    }

    /**
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []) : bool
    {
        /** @var NodeEntity $node */
        $node = $this->app->em()->create('XF:Node');
        $node->node_type_id = $this->getNodeTypeId();

        $form = $this->app->formAction();

        $data = $node->getDataRelationOrDefault();
        $node->addCascadedSave($data);
        $form->basicEntitySave($node, $this->getNodeInput());

        $this->setupFormAction($node, $data, $form);

        if (!$form->run(false))
        {
            return false;
        }

        return true;
    }

    public function postSeed(): void
    {
        $this->app->jobManager()->runUnique('permissionRebuild', $this->config('jobMaxRunTime'));
    }
}