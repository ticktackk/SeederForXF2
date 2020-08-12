<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedPage extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'page';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Pages';
    }
}