<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Phrase;
use XF\Service\Conversation\Creator as ConversationCreatorSvc;

/**
 * Class Conversation
 *
 * @package TickTackk\Seeder\Seed
 */
class Conversation extends AbstractSeed
{
    /**
     * @return Phrase
     */
    public function getTitle() : Phrase
    {
        return \XF::phrase('conversations');
    }

    /**
     * @param array|null $errors
     */
    protected function _seed(array &$errors = null) : void
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();

        if ($randomUsers = $this->randomEntities('XF:User', $faker->numberBetween(1, 3), [
            ['user_id', '<>', $visitor->user_id]
        ]))
        {
            /** @var ConversationCreatorSvc $creator */
            $creator = $this->service('XF:Conversation\Creator', $visitor);
            $creator->setIsAutomated();
            $creator->setContent(Lorem::sentence(), $faker->text);
            if ($faker->boolean)
            {
                $creator->setLogIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
            }
            $creator->setRecipientsTrusted($randomUsers);
            if ($creator->validate($errors))
            {
                $creator->save();
            }
        }
    }
}