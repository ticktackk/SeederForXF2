<?php

namespace TickTackk\Seeder\Repository;

use TickTackk\Seeder\Seed\AbstractSeed;
use XF\Mvc\Entity\Repository;
use function in_array;

/**
 * Class Seed
 *
 * @package TickTackk\Seeder\Repository
 */
class Seed extends Repository
{
    /**
     * @param string $seed
     *
     * @return bool
     */
    public function isValidSeed(string $seed) : bool
    {
        $availableSeeds = $this->getAvailableSeeds();
        return in_array($seed, $availableSeeds, true);
    }

    /**
     * @return array
     */
    public function getAvailableSeeds() : array
    {
        $availableSeeds = [];

        $this->app()->fire('seed_list', [$this->app(), &$availableSeeds]);

        return $availableSeeds;
    }

    /**
     * @param string $seedName
     * @param bool $throw
     *
     * @return AbstractSeed|null
     * @throws \Exception
     */
    public function getSeedHandler(string $seedName, bool $throw = true) :? AbstractSeed
    {
        $seedClass = \XF::stringToClass($seedName, '%s\Seed\%s');
        if (!class_exists($seedClass))
        {
            if ($throw)
            {
                throw new \InvalidArgumentException("Seed handler does not exist: $seedClass");
            }

            return null;
        }

        $seedClass = \XF::extendClass($seedClass);
        return new $seedClass($this->app());
    }
}