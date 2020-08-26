<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedProfilePostCommentReaction extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'profile-post-comment-reaction';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Profile post comment reactions';
    }
}