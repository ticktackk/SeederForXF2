<?php

namespace TickTackk\Seeder\Seed;

class MediaGalleryItemReaction extends AbstractContentReaction
{
    protected function getEntityShortName() : string
    {
        return 'XFMG:MediaItem';
    }
    protected function getUserIdColumn() : string
    {
        return 'user_id';
    }

    protected function getReactionRelationName() : string
    {
        return 'Reactions';
    }
}