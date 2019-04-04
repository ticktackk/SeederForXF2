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
     * Post constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit($this->finder('XF:Thread')->total() * $this->options()->discussionsPerPage);
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle() : \XF\Phrase
    {
        return $this->app->getContentTypePhrase('post', true);
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