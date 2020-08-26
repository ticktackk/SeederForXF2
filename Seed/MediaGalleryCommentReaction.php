<?php

namespace TickTackk\Seeder\Seed;

class MediaGalleryCommentReaction extends AbstractContentReaction
{
    protected function getEntityShortName(): string
    {
        return 'XFMG:Comment';
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