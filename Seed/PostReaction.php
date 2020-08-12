<?php

namespace TickTackk\Seeder\Seed;

class PostReaction extends AbstractContentReaction
{
    protected function getEntityShortName(): string
    {
        return 'XF:Post';
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