<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Repository;
use XF\Phrase;
use XF\Repository\ThreadWatch as ThreadWatchRepo;
use XF\Service\Thread\Replier as ThreadReplierSvc;
use XF\Entity\Thread as ThreadEntity;

/**
 * Class Post
 *
 * @package TickTackk\Seeder\Seed
 */
class Post extends AbstractSeed
{
    /**
     * @return Phrase
     */
    public function getTitle() : Phrase
    {
        return $this->app->getContentTypePhrase('post', true);
    }

    /**
     * @param array|null $errors
     */
    protected function _seed(array &$errors = null) : void
    {
        /** @var ThreadEntity $randomThread */
        $randomThread = $this->randomEntity('XF:Thread');
        if ($randomThread)
        {
            $faker = $this->faker();

            /** @var ThreadReplierSvc $threadReplier */
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
            }
        }
    }

    /**
     * @return Repository|ThreadWatchRepo
     */
    protected function getThreadWatchRepo() : ThreadWatchRepo
    {
        return $this->repository('XF:ThreadWatch');
    }
}