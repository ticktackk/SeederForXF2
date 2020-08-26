<?php

namespace TickTackk\Seeder\Seed;

class ProfilePostReaction extends AbstractContentReaction
{
    protected function getEntityShortName() : string
    {
        return 'XF:ProfilePost';
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