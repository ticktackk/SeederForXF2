<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryCommentReaction extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'media-gallery-comment-reaction';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Media gallery comment reactions';
    }
}