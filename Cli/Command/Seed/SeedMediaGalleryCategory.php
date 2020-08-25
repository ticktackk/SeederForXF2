<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryCategory extends AbstractSeedCommand
{
    protected function getSeedName() : string
    {
        return 'media-gallery-category';
    }

    protected function getContentTypePlural(InputInterface $input = null) : string
    {
        return 'Media gallery categories';
    }
}