<?php

namespace TickTackk\Seeder\Seed;

use XF\Service\Conversation\Replier as ConversationReplier;
use XF\Entity\ConversationRecipient as ConversationRecipientEntity;

class ConversationMessage extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();

        /** @var ConversationRecipientEntity $conversationRecipient */
        $conversationRecipient = $conversationRecipient = $this->randomEntity('XF:ConversationRecipient', [
            ['user_id', $visitor->user_id]
        ], [
            ['Conversation', true]
        ]);

        if (!$conversationRecipient)
        {
            return false;
        }

        /** @var ConversationReplier $replier */
        $replier = $this->service('XF:Conversation\Replier', $conversationRecipient->Conversation, $visitor);
        $replier->setIsAutomated();
        if ($faker->boolean)
        {
            $replier->setLogIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
        }
        $replier->setMessageContent($faker->text);
        if (!$replier->validate())
        {
            return false;
        }

        if (!$replier->save())
        {
            return false;
        }

        return true;
    }
}