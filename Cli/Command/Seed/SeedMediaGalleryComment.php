<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryComment extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'media-gallery-comment';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Media gallery comments';
    }
}