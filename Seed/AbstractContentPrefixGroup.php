<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Entity;
use XF\Entity\AbstractPrefixGroup as AbstractPrefixGroupEntity;

/**
 * @since 1.1.0 Release Candidate 1
 */
abstract class AbstractContentPrefixGroup extends AbstractSeed
{
    abstract protected function getClassIdentifier() : string;

    /**
     * @return Entity|AbstractPrefixGroupEntity
     */
    protected function getEntity() : AbstractPrefixGroupEntity
    {
        return $this->em()->create($this->getClassIdentifier());
    }

    protected function getInput() : array
    {
        return [
            'display_order' => $this->faker()->randomNumber()
        ];
    }

    /**
     * @param array $params
     *
     * @return bool
     *
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []): bool
    {
        $formAction = $this->formAction();
        $prefixGroup = $this->getEntity();

        $formAction->basicEntitySave($prefixGroup, $this->getInput());

        $formAction->apply(function() use ($prefixGroup)
        {
            $phrase = $prefixGroup->getMasterPhrase();
            $phrase->phrase_text = $this->faker()->words(3, true);
            $phrase->save();
        });

        if (!$formAction->run(false))
        {
            return false;
        }

        return true;
    }
}