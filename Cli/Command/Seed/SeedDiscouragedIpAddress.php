<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedDiscouragedIpAddress extends AbstractSeedCommand
{
    protected function getSeedName(): string
    {
        return 'discouraged-ip-address';
    }

    protected function getContentTypePlural(InputInterface $input = null): string
    {
        return 'Discouraged IP addresses';
    }
}