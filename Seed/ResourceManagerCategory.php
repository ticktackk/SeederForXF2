<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Entity\Forum as ForumEntity;

class ResourceManagerCategory extends AbstractCategoryTree
{
    protected function getEntityIdentifier(): string
    {
        return 'XFRM:Category';
    }

    protected function getCategoryInput(): array
    {
        $categoryInput = parent::getCategoryInput();

        $faker = $this->faker();

        $categoryInput['title'] = \implode(' ', Lorem::words());

        if ($faker->boolean)
        {
            $categoryInput['description'] = $faker->paragraph;
        }

        if ($faker->boolean)
        {
            $categoryInput['min_tags'] = $faker->numberBetween($faker->numberBetween(1, 5), $faker->numberBetween(10, 20));
        }

        if ($faker->boolean)
        {
            /** @var ForumEntity $randomForum */
            $randomForum = $this->finderWithRandomOrder('XF:Forum')->fetchOne();
            if ($randomForum && $faker->boolean)
            {
                $categoryInput['thread_node_id'] = $randomForum->node_id;
                $categoryInput['thread_prefix_id'] = 0;

                $threadPrefixIds = \array_keys($randomForum->prefix_cache);
                if (\count($threadPrefixIds) && $faker->boolean)
                {
                    $categoryInput['thread_prefix_id'] = \array_rand($threadPrefixIds);
                }
            }
        }

        $categoryInput['allow_local'] = $faker->boolean;
        $categoryInput['allow_external'] = $faker->boolean;
        $categoryInput['allow_commercial_external'] = $faker->boolean;
        $categoryInput['enable_versioning'] = $faker->boolean;
        $categoryInput['enable_support_url'] = $faker->boolean;
        $categoryInput['always_moderate_create'] = $faker->boolean;
        $categoryInput['always_moderate_update'] = $faker->boolean;
        $categoryInput['require_prefix'] = $faker->boolean;

        do
        {
            $categoryInput['allow_local'] = $faker->boolean;
            if ($categoryInput['allow_local'])
            {
                break;
            }

            $categoryInput['allow_external'] = $faker->boolean;
            if ($categoryInput['allow_external'])
            {
                break;
            }

            $categoryInput['allow_commercial_external'] = $faker->boolean;
            if ($categoryInput['allow_commercial_external'])
            {
                break;
            }
        }
        while (!$categoryInput['allow_local'] && !$categoryInput['allow_external'] && !$categoryInput['allow_commercial_external']);

        return $categoryInput;
    }
}