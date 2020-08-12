<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedCategory extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'category';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Categories';
    }
}