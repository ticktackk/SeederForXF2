<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryAlbum extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'media-gallery-album';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Media gallery albums';
    }
}