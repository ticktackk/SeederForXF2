<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedPost extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'post';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Posts';
    }
}