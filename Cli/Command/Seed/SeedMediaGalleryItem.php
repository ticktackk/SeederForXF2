<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryItem extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'media-gallery-item';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Media gallery media items';
    }
}