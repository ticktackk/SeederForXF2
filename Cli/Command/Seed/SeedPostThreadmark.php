<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedPostThreadmark extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'post-threadmark';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Post threadmarks';
    }
}