<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class ConversationMessage
 *
 * @package TickTackk\Seeder\Seed
 */
class ConversationMessage extends AbstractSeed
{
    /**
     * ConversationMessage constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        parent::__construct($app);

        $this->setLimit(
            $this->finder('XF:ConversationMaster')->total() * $this->options()->messagesPerPage
        );
    }

    /**
     * @return \XF\Phrase
     */
    public function getTitle(): \XF\Phrase
    {
        return \XF::phrase('conversation_messages');
    }

    /**
     * @param array|null $errors
     */
    protected function seedInternal(array &$errors = null): void
    {
        $visitor = \XF::visitor();
        $faker = $this->faker();

        /** @var \XF\Entity\ConversationRecipient $conversationRecipient */
        if ($conversationRecipient = $this->randomEntity('XF:ConversationRecipient', [
            ['user_id', $visitor->user_id]
        ], [
            ['Conversation', true]
        ]))
        {
            /** @var \XF\Service\Conversation\Replier $replier */
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