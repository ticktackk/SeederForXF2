<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedThread extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'thread';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Threads';
    }
}