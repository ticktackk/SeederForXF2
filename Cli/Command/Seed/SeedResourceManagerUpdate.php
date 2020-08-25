<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedResourceManagerUpdate extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'resource-manager-update';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Resource manager updates';
    }
}