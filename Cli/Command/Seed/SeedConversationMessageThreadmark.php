<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedConversationMessageThreadmark extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'conversation-message-threadmark';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Conversation message threadmarks';
    }
}