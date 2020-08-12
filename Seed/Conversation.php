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
    protected function seed(array $params = []): bool
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();

        $randomUsers = $this->randomEntities('XF:User', $faker->numberBetween(1, 3), [
            ['user_id', '<>', $visitor->user_id]
        ]);
        if (!$randomUsers->count())
        {
            return false;
        }

        /** @var ConversationCreatorSvc $creator */
        $creator = $this->service('XF:Conversation\Creator', $visitor);
        $creator->setIsAutomated();
        $creator->setContent(Lorem::sentence(), $faker->text);
        if ($faker->boolean)
        {
            $creator->setLogIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
        }

        $creator->setRecipientsTrusted($randomUsers);
        if (!$creator->validate())
        {
            return false;
        }

        if (!$creator->save())
        {
            return false;
        }

        return true;
    }
}