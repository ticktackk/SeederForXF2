<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedUserReject extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'user-reject';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'User rejects';
    }
}