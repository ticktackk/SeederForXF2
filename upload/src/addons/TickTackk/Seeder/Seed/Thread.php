<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;

/**
 * Class Thread
 *
 * @package TickTackk\Seeder\Seed
 */
class Thread extends AbstractSeed
{
    /**
     * @return int
     */
    public function getRunOrder(): int
    {
        return 20;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->faker()->numberBetween(5000, 10000);
    }

    /**
     * @param array|null $errors
     */
    protected function seedInternal(array &$errors = null) : void
    {
        if ($randomForum = $this->randomEntity('XF:Forum'))
        {
            $faker = $this->faker();

            /** @var \XF\Service\Thread\Creator $threadCreator */
            $threadCreator = $this->service('XF:Thread\Creator', $randomForum);
            $threadCreator->setIsAutomated();

            if ($faker->boolean)
            {
                $threadCreator->logIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
            }

            $threadCreator->setTags($faker->words($faker->numberBetween(10, 15)));
            $threadCreator->setContent(Lorem::sentence(), $faker->text);
            if ($threadCreator->validate($errors))
            {
                $threadCreator->save();
            }
        }
    }
}