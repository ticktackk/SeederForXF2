<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedUser extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'user';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Users';
    }
}