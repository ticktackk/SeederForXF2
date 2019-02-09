<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class Post
 *
 * @package TickTackk\Seeder\Seed
 */
class Post extends AbstractSeed
{
    /**
     * @return int
     */
    public function getRunOrder(): int
    {
        return 30;
    }

    /**
     * @return int
     */
    public function getLimit(): int
    {
        return $this->faker()->numberBetween(4500, 6000);
    }

    /**
     * @param array|null $errors
     */
    protected function seedInternal(array &$errors = null) : void
    {
        if ($randomThread = $this->randomEntity('XF:Thread'))
        {
            $faker = $this->faker();

            /** @var \XF\Service\Thread\Replier $threadReplier */
            $threadReplier = $this->service('XF:Thread\Replier', $randomThread);
            $threadReplier->setIsAutomated();
            if ($faker->boolean)
            {
                $threadReplier->logIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
            }
            $threadReplier->setMessage($faker->text);
            if ($threadReplier->validate($errors))
            {
                $threadReplier->save();
            }
        }
    }
}