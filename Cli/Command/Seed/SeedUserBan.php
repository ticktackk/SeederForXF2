<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedUserBan extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'user-ban';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'User bans';
    }
}