<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedResourceManagerCategory extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'resource-manager-category';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Resource manager categories';
    }
}