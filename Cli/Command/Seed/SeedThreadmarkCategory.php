<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedThreadmarkCategory extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'threadmark-category';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Threadmark categories';
    }
}