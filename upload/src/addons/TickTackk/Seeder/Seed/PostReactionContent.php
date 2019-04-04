<?php

namespace TickTackk\Seeder\Seed;

/**
 * Class PostReactionContent
 *
 * @package TickTackk\Seeder\Seed
 */
class PostReactionContent extends AbstractReactionContent
{
    /**
     * @return string
     */
    protected function getEntityShortName(): string
    {
        return 'XF:Post';
    }

    /**
     * @return string
     */
    protected function getUserIdColumn(): string
    {
        return 'user_id';
    }

    /**
     * @return string
     */
    protected function getReactionRelationName(): string
    {
        return 'Reactions';
    }
}