<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedProfilePostComment extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'profile-post-comment';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Profile post comments';
    }
}