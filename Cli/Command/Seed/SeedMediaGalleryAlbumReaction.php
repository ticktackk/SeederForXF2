<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryAlbumReaction extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'media-gallery-album-reaction';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Media gallery album reactions';
    }
}