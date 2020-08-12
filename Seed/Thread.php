<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Mvc\Entity\Repository;
use XF\Phrase;
use XF\Repository\ThreadWatch as ThreadWatchRepo;
use XF\Service\Thread\Creator as ThreadCreatorSvc;
use XF\Repository\Thread as ThreadRepo;

/**
 * Class Thread
 *
 * @package TickTackk\Seeder\Seed
 */
class Thread extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        /** @var \XF\Entity\Forum $randomForum */
        $randomForum = $this->randomEntity('XF:Forum');
        if (!$randomForum)
        {
            return false;
        }

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
            return false;
        }

        if (!$threadCreator->save())
        {
            return false;
        }

        $threadWatchRepo = $this->getThreadWatchRepo();
        $threadRepo = $this->getThreadRepo();

        $thread = $threadCreator->getThread();
        $visitor = \XF::visitor();

        if ($this->faker()->boolean)
        {
            if ($this->faker()->boolean)
            {
                $watchState = $this->faker()->boolean ? 'watch_email' : 'watch_no_email';
                $threadWatchRepo->setWatchState($thread, $visitor, $watchState);
            }
        }
        else
        {
            // use user preferences
            $threadWatchRepo->autoWatchThread($thread, $visitor, true);
        }

        $threadRepo->markThreadReadByVisitor($thread, $thread->post_date);

        return true;
    }

    /**
     * @return Repository|ThreadWatchRepo
     */
    protected function getThreadWatchRepo() : ThreadWatchRepo
    {
        return $this->repository('XF:ThreadWatch');
    }

    /**
     * @return Repository|ThreadRepo
     */
    protected function getThreadRepo() : ThreadRepo
    {
        return $this->repository('XF:Thread');
    }
}