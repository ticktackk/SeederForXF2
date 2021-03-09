<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedSpamTrigger extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'spam-trigger';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Spam triggers';
    }
}