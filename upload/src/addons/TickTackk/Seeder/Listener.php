<?php

namespace TickTackk\Seeder;

/**
 * Class Listener
 *
 * @package TickTackk\Seeder
 */
class Listener
{
    /**
     * @param \XF\Cli\App $app
     * @param array       $seeds
     */
    public static function seedList(\XF\App $app, array &$seeds) : void
    {
        foreach (['User', 'Category', 'Forum', 'Page', 'Thread', 'Post', 'PostReactionContent'] AS $className)
        {
            $seeds[] = 'TickTackk\Seeder:' . $className;
        }
    }
}