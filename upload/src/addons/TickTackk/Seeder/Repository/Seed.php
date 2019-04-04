<?php

namespace TickTackk\Seeder\Repository;

use TickTackk\Seeder\Seed\AbstractSeed;
use XF\Mvc\Entity\Repository;

/**
 * Class Seed
 *
 * @package TickTackk\Seeder\Repository
 */
class Seed extends Repository
{
    /**
     * @param string $seedName
     *
     * @return AbstractSeed
     * @throws \Exception
     */
    public function getSeedHandler(string $seedName) : AbstractSeed
    {
        $seedClass = \XF::stringToClass($seedName, '%s\Seed\%s');
        if (!class_exists($seedClass))
        {
            throw new \InvalidArgumentException("Seed handler does not exist: $seedClass");
        }

        $seedClass = \XF::extendClass($seedClass);
        return new $seedClass($this->app());
    }
}