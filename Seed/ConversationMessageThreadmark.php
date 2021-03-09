<?php

namespace TickTackk\Seeder\Seed;

use SV\Threadmarks\XF\Entity\ConversationMessage as ExtendedConversationMessageEntityFromThreadmarks;

class ConversationMessageThreadmark extends AbstractContentThreadmark
{
    /**
     * @inheritDoc
     */
    protected function findRandomContentAndContainer() :? array
    {
        /** @var ExtendedConversationMessageEntityFromThreadmarks $conversationMessage */
        $conversationMessage = $this->finderWithRandomOrder('XF:ConversationMessage')
            ->where('Threadmark.threadmark_id', null)
            ->where('user_id', \XF::visitor()->user_id)
            ->with('Conversation', true)
            ->fetchOne();

        if (!$conversationMessage)
        {
            return null;
        }

        return [
            $conversationMessage,
            $conversationMessage->Conversation
        ];
    }
}