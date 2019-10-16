<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\ArrayCollection;
use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XF\PrintableException;

/**
 * Class AbstractSeed
 *
 * @package TickTackk\Seeder\Seed
 */
abstract class AbstractSeed
{
    /**
     * @var \XF\App
     */
    protected $app;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * @var int
     */
    protected $done = 0;

    /**
     * @var int
     */
    protected $limit = 100;

    /**
     * AbstractSeed constructor.
     *
     * @param \XF\App $app
     */
    public function __construct(\XF\App $app)
    {
        $this->app = $app;
    }

    /**
     * @return \XF\Phrase
     */
    abstract public function getTitle() : \XF\Phrase;

    /**
     * @param int $done
     */
    public function setDone(int $done) : void
    {
        $this->done = $done;
    }

    /**
     * @return int
     */
    public function getDone() : int
    {
        return $this->done;
    }

    /**
     * @param int $limit
     */
    public function setLimit(int $limit) : void
    {
        $this->limit = $limit;
    }

    /**
     * @return int
     */
    public function getLimit() : int
    {
        return $this->limit;
    }

    /**
     * @param array|null $errors
     */
    abstract protected function seedInternal(array &$errors = null) : void;

    /**
     * @return mixed
     * @throws PrintableException
     * @throws \Exception
     */
    public function run()
    {
        $errors = [];

        /** @var \XF\Entity\User $randomUser */
        $randomUser = $this->randomEntity('XF:User');
        $result = \XF::asVisitor($randomUser, function ()
        {
            $this->seedInternal($errors);
        });

        if (\is_array($errors) && \count($errors))
        {
            throw new PrintableException(implode("\n", $errors));
        }

        return $result;
    }

    /**
     * @param string $identifier
     * @param array  $whereArr
     * @param array $withArr
     *
     * @return null|Entity
     */
    protected function randomEntity(string $identifier, array $whereArr = [], array $withArr = []) :? Entity
    {
        $randomEntities = $this->randomEntities($identifier, 1, $whereArr, $withArr);
        if ($randomEntities->count())
        {
            return $randomEntities->first();
        }

        return null;
    }

    /**
     * @param string      $identifier
     * @param int         $limit
     * @param array       $whereArr
     * @param array       $withArr
     * @param string|null $orderBy
     *
     * @return ArrayCollection
     */
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

            if (\is_array($with) && \count($with) === 2 && is_string(reset($with)) && is_bool(end($with)))
            {
                $mustExist = end($with);
                $with = reset($with);
            }
            $finder->with($with, $mustExist);
        }

        return $finder->fetch();
    }

    /**
     * @return \Faker\Generator
     */
    public function faker(): \Faker\Generator
    {
        if ($this->faker === null)
        {
            $this->faker = \Faker\Factory::create();
        }

        return $this->faker;
    }

    /**
     * @param $class
     *
     * @return \XF\Service\AbstractService
     */
    protected function service(string $class): \XF\Service\AbstractService
    {
        return call_user_func_array([$this->app, 'service'], func_get_args());
    }

    /**
     * @param $identifier
     *
     * @return \XF\Mvc\Entity\Repository
     */
    protected function repository(string $identifier): \XF\Mvc\Entity\Repository
    {
        return $this->app->repository($identifier);
    }

    /**
     * @param string $identifier
     *
     * @return Finder
     */
    protected function finder(string $identifier): Finder
    {
        return $this->app->finder($identifier);
    }

    /**
     * @return \ArrayObject
     */
    protected function options() : \ArrayObject
    {
        return $this->app->options();
    }
}