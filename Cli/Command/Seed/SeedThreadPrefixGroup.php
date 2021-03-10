<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

/**
 * @since 1.1.0 Release Candidate 1
 */
class SeedThreadPrefixGroup extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'thread-prefix-group';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Thread prefix groups';
    }
}