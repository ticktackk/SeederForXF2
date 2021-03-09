<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedSpamCleanerLog extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'spam-cleaner-log';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Spam cleaner log';
    }
}