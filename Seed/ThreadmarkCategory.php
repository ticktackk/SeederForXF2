<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use SV\Threadmarks\Entity\ThreadmarkCategory as ThreadmarkCategoryEntity;

class ThreadmarkCategory extends AbstractSeed
{
    /**
     * @param array $params
     * @return bool
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []): bool
    {
        /** @var ThreadmarkCategoryEntity $threadmarkCategory */
        $threadmarkCategory = $this->em()->create('SV\Threadmarks:ThreadmarkCategory');

        $faker = $this->faker();
        $input = [
            'display_order' => $faker->randomNumber(),
            'is_always_filtered_in_whats_new' => $faker->boolean,
            'is_selected_in_whats_new_by_default' => $faker->boolean,
            'is_selected_in_search_by_default' => $faker->boolean,
            'allowed_user_group_ids' => $faker->boolean ? [-1] : $this->getRandomUserGroupIds()
        ];

        $form = $this->formAction();
        $form->basicEntitySave($threadmarkCategory, $input);

        $form->apply(function () use($threadmarkCategory, $faker)
        {
            $masterTitle = $threadmarkCategory->getMasterPhrase();
            $masterTitle->phrase_text = implode(' ', Lorem::words());
            $masterTitle->save();
        });

        if (!$form->run(false))
        {
            return false;
        }

        return true;
    }
}