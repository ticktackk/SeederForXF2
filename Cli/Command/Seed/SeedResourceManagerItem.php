<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedResourceManagerItem extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'resource-manager-item';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Resource manager items';
    }
}