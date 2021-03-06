<?php

namespace TickTackk\Seeder\Cli\Command\Seed;

use Symfony\Component\Console\Input\InputInterface;

class SeedMediaGalleryItemReaction extends AbstractSeedCommand
{
    protected function getSeedName(): string
    {
        return 'media-gallery-item-reaction';
    }

    protected function getContentTypePlural(InputInterface $input = null): string
    {
        return 'Media gallery item reactions';
    }
}