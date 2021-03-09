<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedIpAddressBan extends AbstractSeedCommand
{
    protected function getSeedName(): string
    {
        return 'ip-address-ban';
    }

    protected function getContentTypePlural(InputInterface $input = null): string
    {
        return 'IP address bans';
    }
}