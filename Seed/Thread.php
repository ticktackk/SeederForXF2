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
     * Thread constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit($this->faker()->numberBetween(5000, 10000));
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle() : \XF\Phrase
    {
        return $this->app->getContentTypePhrase('thread', true);
    }

    /**
     * @param array|null $errors
     */
    protected function seedInternal(array &$errors = null) : void
    {
        /** @var \XF\Entity\Forum $randomForum */
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

            if ($faker->boolean)
            {
                $prefixIds = $randomForum->getPrefixes()->keys();
                if ($prefixIds)
                {
                    $threadCreator->setPrefix($prefixIds[array_rand($prefixIds)]);
                }
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