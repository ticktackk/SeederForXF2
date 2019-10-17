<?php

namespace TickTackk\Seeder\Seed;

use XF\Phrase;
use XF\Service\Conversation\Replier as ConversationReplier;
use XF\Entity\ConversationRecipient as ConversationRecipientEntity;

/**
 * Class ConversationMessage
 *
 * @package TickTackk\Seeder\Seed
 */
class ConversationMessage extends AbstractSeed
{
    /**
     * @return Phrase
     */
    public function getTitle() : Phrase
    {
        return \XF::phrase('conversation_messages');
    }

    /**
     * @param array|null $errors
     */
    protected function _seed(array &$errors = null) : void
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();

        /** @var ConversationRecipientEntity $conversationRecipient */
        if ($conversationRecipient = $this->randomEntity('XF:ConversationRecipient', [
            ['user_id', $visitor->user_id]
        ], [
            ['Conversation', true]
        ]))
        {
            /** @var ConversationReplier $replier */
            $replier = $this->service('XF:Conversation\Replier', $conversationRecipient->Conversation, $visitor);
            $replier->setIsAutomated();
            if ($faker->boolean)
            {
                $replier->setLogIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
            }
            $replier->setMessageContent($faker->text);
            if ($replier->validate($errors))
            {
                $replier->save();
            }
        }
    }
}