<?php

namespace TickTackk\Seeder;

use XF\Cli\App;

/**
 * Class Listener
 *
 * @package TickTackk\Seeder
 */
class Listener
{
    /**
     * @param App   $app
     * @param array $seeds
     */
    public static function seedList(/** @noinspection PhpUnusedParameterInspection */App $app, array &$seeds) : void
    {
        foreach (['User', 'Category', 'Forum', 'Page', 'Thread', 'Post', 'PostReactionContent'] AS $className)
        {
            $seeds[] = 'TickTackk\Seeder:' . $className;
        }
    }
}