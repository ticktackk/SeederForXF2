<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\AbstractCategoryTree as AbstractCategoryTreeEntity;
use XF\Mvc\FormAction;

abstract class AbstractCategoryTree extends AbstractSeed
{
    abstract protected function getEntityIdentifier(): string;

    protected function getCategoryInput(): array
    {
        $categoryInput = ['parent_category_id' => 0];

        if ($this->faker()->boolean)
        {
            $randomCategory = $this->getRandomCategory();

            $categoryInput['parent_category_id'] = $randomCategory
                ? $randomCategory->getEntityId()
                : 0;
        }

        return $categoryInput;
    }

    protected function getRandomCategory() :? AbstractCategoryTreeEntity
    {
        return $this->finderWithRandomOrder($this->getEntityIdentifier())->fetchOne();
    }

    protected function validate(FormAction $formAction, array $categoryInput) : void
    {
    }

    protected function postBasicEntitySave(FormAction $formAction, AbstractCategoryTreeEntity $categoryTree, array $categoryInput) : void
    {
    }

    /**
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []): bool
    {
        /** @var AbstractCategoryTreeEntity $category */
        $category = $this->em()->create($this->getEntityIdentifier());
        $categoryInput = $this->getCategoryInput();

        $formAction = $this->app()->formAction();
        $formAction->validate(function (FormAction $formAction) use($categoryInput)
        {
            $this->validate($formAction, $categoryInput);
        });

        $formAction->basicEntitySave($category, $categoryInput);

        $this->postBasicEntitySave($formAction, $category, $categoryInput);

        if (!$formAction->run(false))
        {
            return false;
        }

        return true;
    }
}