<?php

namespace TickTackk\Seeder\Seed;

use ArrayObject;
use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\Mvc\Entity\Manager as EntityManager;
use XF\Mvc\Entity\Repository;
use XF\App as BaseApp;
use Faker\Generator as FakerGenerator;
use Faker\Factory as FakerFactory;
use XF\Service\AbstractService;

/**
 * Class AbstractSeed
 *
 * @package TickTackk\Seeder\Seed
 */
abstract class AbstractSeed
{
    /**
     * @var BaseApp
     */
    protected $app;

    /**
     * @var FakerGenerator
     */
    protected $faker;

    public function __construct(BaseApp $app)
    {
        $this->app = $app;

        \XF::$time = \time();
    }

    abstract protected function seed(array $params = []) : bool;

    /**
     * @throws \Exception
     */
    public function insert(?array $params = []) : bool
    {
        /** @var \XF\Entity\User $randomUser */
        $randomUser = $this->finderWithRandomOrder('XF:User')->fetchOne();

        try
        {
            return \XF::asVisitor($randomUser, function () use($params)
            {
                return $this->seed($params);
            });
        }
        finally
        {
            $this->em()->clearEntityCache();
        }
    }

    protected function randomEntity(string $identifier, array $whereArr = [], array $withArr = []) :? Entity
    {
        $randomEntities = $this->randomEntities($identifier, 1, $whereArr, $withArr);

        if ($randomEntities->count())
        {
            return $randomEntities->first();
        }

        return null;
    }

    protected function randomEntities(string $identifier, int $limit, array $whereArr = [], array $withArr = [], string $orderBy = null) : ArrayCollection
    {
        $finder = $this->finder($identifier)
            ->order($orderBy ?: Finder::ORDER_RANDOM)
            ->limit($limit);

        foreach ($whereArr AS $where)
        {
            $finder->where($where);
        }

        foreach ($withArr AS $with)
        {
            $mustExist = false;

            if (is_array($with) && count($with) === 2 && is_string(reset($with)) && is_bool(end($with)))
            {
                $mustExist = end($with);
                $with = reset($with);
            }
            $finder->with($with, $mustExist);
        }

        return $finder->fetch();
    }

    public function faker(): FakerGenerator
    {
        if ($this->faker === null)
        {
            $this->faker = FakerFactory::create();
        }

        return $this->faker;
    }

    protected function service(string $class, ...$arguments) : AbstractService
    {
        return $this->app()->service($class, ...$arguments);
    }

    protected function repository(string $identifier): Repository
    {
        return $this->app()->repository($identifier);
    }

    protected function finder(string $identifier): Finder
    {
        return $this->app()->finder($identifier);
    }

    protected function options() : ArrayObject
    {
        return $this->app()->options();
    }

    /**
     * @return mixed
     */
    protected function config(string $key = null)
    {
        return $this->app()->config($key);
    }

    protected function em() : EntityManager
    {
        return $this->app()->em();
    }

    protected function app() : BaseApp
    {
        return $this->app;
    }
}