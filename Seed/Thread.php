<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Phrase;
use XF\Service\Thread\Creator as ThreadCreatorSvc;

/**
 * Class Thread
 *
 * @package TickTackk\Seeder\Seed
 */
class Thread extends AbstractSeed
{
    /**
     * @return Phrase
     */
    public function getTitle() : Phrase
    {
        return $this->app->getContentTypePhrase('thread', true);
    }

    /**
     * @param array|null $errors
     */
    protected function _seed(array &$errors = null) : void
    {
        /** @var \XF\Entity\Forum $randomForum */
        if ($randomForum = $this->randomEntity('XF:Forum'))
        {
            $faker = $this->faker();

            /** @var ThreadCreatorSvc $threadCreator */
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