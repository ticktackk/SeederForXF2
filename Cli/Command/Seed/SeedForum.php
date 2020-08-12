<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedForum extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'forum';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Forums';
    }
}