<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;

class MediaGalleryCategory extends AbstractCategoryTree
{
    protected function getEntityIdentifier(): string
    {
        return 'XFMG:Category';
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

        $categoryInput['category_type'] = null;
        do
        {
            if ($faker->boolean)
            {
                $categoryInput['category_type'] = 'album';
            }
            else if ($faker->boolean)
            {
                $categoryInput['category_type'] = 'media';
            }
            else if ($faker->boolean)
            {
                $categoryInput['category_type'] = 'container';
            }
        }
        while ($categoryInput['category_type'] === null);

        $categoryInput['allowed_types'] = [];
        $setupAllowedTypes = function () use($faker, $categoryInput)
        {
            foreach (['image', 'video', 'audio', 'embed'] AS $allowedType)
            {
                if ($faker->boolean)
                {
                    $categoryInput['allowed_types'][] = $allowedType;
                }
            }
        };

        $setupAllowedTypes();

        if (\in_array($categoryInput['category_type'], ['album', 'media'], true) && !\count($categoryInput['allowed_types']))
        {
            do
            {
                $setupAllowedTypes();
            }
            while (\count($categoryInput['allowed_types']) === 0);
        }

        return $categoryInput;
    }
}