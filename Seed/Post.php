<?php

namespace TickTackk\Seeder\Seed;

use XF\Repository\ThreadWatch as ThreadWatchRepo;
use XF\Service\Thread\Replier as ThreadReplierSvc;

class Post extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        $randomThread = $this->finderWithRandomOrder('XF:Thread')->fetchOne();
        if (!$randomThread)
        {
            return false;
        }

        $faker = $this->faker();

        /** @var ThreadReplierSvc $threadReplier */
        $threadReplier = $this->service('XF:Thread\Replier', $randomThread);
        $threadReplier->setIsAutomated();

        if ($faker->boolean)
        {
            $threadReplier->logIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
        }

        $threadReplier->setMessage($faker->text);

        if (!$threadReplier->validate())
        {
            return false;
        }

        $threadReplier->save();
        $threadReplier->sendNotifications();

        if ($this->faker()->boolean)
        {
            $threadWatchRepo = $this->getThreadWatchRepo();
            $visitor = \XF::visitor();

            if ($this->faker()->boolean)
            {
                $watchState = $this->faker()->boolean ? 'watch_no_email' : 'watch_email';
                $threadWatchRepo->setWatchState($randomThread, $visitor, $watchState);
            }
            else
            {
                // use user preferences
                $threadWatchRepo->autoWatchThread($randomThread, $visitor, false);
            }
        }

        return true;
    }

    protected function getThreadWatchRepo() : ThreadWatchRepo
    {
        return $this->repository('XF:ThreadWatch');
    }
}