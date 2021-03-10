<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\AbstractPrefixGroup as AbstractPrefixGroupEntity;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Repository;
use XF\Repository\AbstractPrefix as AbstractPrefixRepo;
use XF\Entity\AbstractPrefix as AbstractPrefixEntity;
use XF\Repository\AbstractPrefixMap as AbstractPrefixMapRepo;

/**
 * @since 1.1.0 Release Candidate 1
 */
abstract class AbstractContentPrefix extends AbstractSeed
{
    abstract protected function getClassIdentifier() : string;

    abstract protected function getGroupClassIdentifier() : string;

    abstract protected function getMapIdentifier() : string;

    abstract protected function getContainerIdentifier() : string;

    protected function getRandomContainerIds() : array
    {
        $containerIds = $this->finderWithRandomOrder($this->getContainerIdentifier())->fetch()->keys();
        \shuffle($containerIds);

        return \array_slice($containerIds, $this->faker()->numberBetween(1, \count($containerIds)));
    }

    /**
     * @return Entity|AbstractPrefixEntity
     */
    protected function getEntity() : AbstractPrefixEntity
    {
        return $this->em()->create($this->getClassIdentifier());
    }

    /**
     * @return Repository|AbstractPrefixRepo
     */
    protected function getRepo() : AbstractPrefixRepo
    {
        return $this->repository($this->getClassIdentifier());
    }

    /**
     * @return Repository|AbstractPrefixMapRepo
     */
    protected function getMapRepo() : AbstractPrefixMapRepo
    {
        return $this->repository($this->getMapIdentifier());
    }

    protected function getRandomCssClass() : string
    {
        $faker = $this->faker();

        $cssClass = '';

        if ($faker->boolean)
        {
            $cssClasses = $this->getRepo()->getDefaultDisplayStyles();
            if (\count($cssClasses))
            {
                \shuffle($cssClasses);
                $cssClass = $cssClasses[\array_rand($cssClasses)];
            }
        }
        else
        {
            $cssClass = 'label label--' . $faker->word;
        }

        return \substr($cssClass, 0, 50);
    }

    /**
     * @return Entity|AbstractPrefixGroupEntity|null
     */
    protected function getRandomPrefixGroup() :? AbstractPrefixGroupEntity
    {
        if (!$this->faker()->boolean)
        {
            return null;
        }

        return $this->finderWithRandomOrder($this->getGroupClassIdentifier())->fetchOne();
    }

    protected function getRandomPrefixGroupId() : int
    {
        $randomPrefixGroup = $this->getRandomPrefixGroup();
        if (!$randomPrefixGroup)
        {
            return 0;
        }

        return $randomPrefixGroup->prefix_group_id;
    }

    protected function getAllowedUserGroupIds() : array
    {
        return $this->faker()->boolean ? [-1] : $this->getRandomUserGroupIds();
    }

    protected function getInput() : array
    {
        return [
            'css_class' => $this->getRandomCssClass(),
            'prefix_group_id' => $this->getRandomPrefixGroupId(),
            'display_order' => $this->faker()->randomNumber(),
            'allowed_user_group_ids' => $this->getAllowedUserGroupIds()
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
        $faker = $this->faker();
        $formAction = $this->formAction();
        $prefix = $this->getEntity();

        $formAction->basicEntitySave($prefix, $this->getInput());

        $formAction->apply(function() use ($faker, $prefix)
        {
            $phrase = $prefix->getMasterPhrase();
            $phrase->phrase_text = $faker->words(3, true);
            $phrase->save();
        });

        if (\XF::$versionId >= 2020010)
        {
            if ($faker->boolean)
            {
                $formAction->apply(function() use ($faker, $prefix)
                {
                    $phrase = $prefix->getDescriptionMasterPhrase();
                    $phrase->phrase_text = $faker->text;
                    $phrase->save();
                });
            }

            if ($faker->boolean)
            {
                $formAction->apply(function() use ($faker, $prefix)
                {
                    $phrase = $prefix->getUsageHelpMasterPhrase();
                    $phrase->phrase_text = $faker->text;
                    $phrase->save();
                });
            }
        }

        $formAction->complete(function () use($prefix)
        {
            $this->getMapRepo()->updatePrefixAssociations(
                $prefix,
                $this->getRandomContainerIds()
            );
        });

        if (!$formAction->run(false))
        {
            return false;
        }

        return true;
    }
}