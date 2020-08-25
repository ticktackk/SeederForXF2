<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedPostReaction extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'post-reaction';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Post reactions';
    }
}