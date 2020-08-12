<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedConversationMessage extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'conversation-message';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Conversation messages';
    }
}