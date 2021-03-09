<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedEmailBounceLog extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'email-bounce-log';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Email bounce logs';
    }
}