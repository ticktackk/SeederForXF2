<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedErrorLog extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'error-log';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Error logs';
    }
}