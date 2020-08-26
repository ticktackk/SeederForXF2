<?php

namespace TickTackk\Seeder\Seed;

class ProfilePostCommentReaction extends AbstractContentReaction
{
    protected function getEntityShortName() : string
    {
        return 'XF:ProfilePostComment';
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