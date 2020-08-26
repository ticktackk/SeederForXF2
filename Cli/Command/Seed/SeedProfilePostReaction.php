<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedProfilePostReaction extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'profile-post-reaction';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Profile post reactions';
    }
}