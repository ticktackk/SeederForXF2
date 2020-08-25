<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedConversation extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'conversation';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Conversations';
    }
}