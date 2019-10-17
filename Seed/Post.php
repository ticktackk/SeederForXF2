<?php

namespace TickTackk\Seeder\Seed;

use XF\Phrase;
use XF\Service\Thread\Replier as ThreadReplierSvc;

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
        if ($randomThread = $this->randomEntity('XF:Thread'))
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
            }
        }
    }
}