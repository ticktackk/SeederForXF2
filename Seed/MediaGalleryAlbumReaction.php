<?php

namespace TickTackk\Seeder\Seed;

class MediaGalleryAlbumReaction extends AbstractContentReaction
{
    protected function getEntityShortName(): string
    {
        return 'XFMG:Album';
    }

    protected function getUserIdColumn(): string
    {
        return 'user_id';
    }

    protected function getReactionRelationName(): string
    {
        return 'Reactions';
    }
}