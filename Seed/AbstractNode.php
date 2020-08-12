<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Entity\Node as NodeEntity;

/**
 * Class AbstractNode
 *
 * @package TickTackk\Seeder\Seed
 */
abstract class AbstractNode extends AbstractSeed
{
    /**
     * @return string
     */
    abstract protected function getNodeTypeId() : string;

    /**
     * @return null|\XF\Entity\AbstractNode
     */
    protected function getRandomParentNode() :? \XF\Entity\AbstractNode
    {
        return null;
    }

    /**
     * @return array
     */
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
     * @param array $params
     *
     * @return bool
     *
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