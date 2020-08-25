<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Service\Conversation\Creator as ConversationCreatorSvc;

class Conversation extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();
        
        $randomUsers = $this->finderWithRandomOrder('XF:User')
            ->where('user_id', '<>', $visitor->user_id)
            ->limit($faker->numberBetween(1, 3))
            ->fetch();
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