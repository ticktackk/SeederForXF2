<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedProfilePost extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'profile-post';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Profile posts';
    }
}