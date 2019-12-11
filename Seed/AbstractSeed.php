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
use XF\Phrase;
use Faker\Factory as FakerFactory;
use XF\Service\AbstractService;
use function count;
use function is_array;
use function is_bool;
use function is_string;

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

    /**
     * AbstractSeed constructor.
     *
     * @param BaseApp $app
     */
    public function __construct(BaseApp $app)
    {
        $this->app = $app;
    }

    /**
     * @return Phrase
     */
    abstract public function getTitle() : Phrase;

    /**
     * @param array|null $errors
     */
    abstract protected function _seed(array &$errors = null) : void;

    /**
     * @param array $errors
     *
     * @throws \Exception
     */
    public function seed(array &$errors = null) : void
    {
        /** @var \XF\Entity\User $randomUser */
        $randomUser = $this->randomEntity('XF:User');

        \XF::asVisitor($randomUser, function () use($errors)
        {
            $this->_seed($errors);
        });
    }

    public function postSeed() : void
    {
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

            if (is_array($with) && count($with) === 2 && is_string(reset($with)) && is_bool(end($with)))
            {
                $mustExist = end($with);
                $with = reset($with);
            }
            $finder->with($with, $mustExist);
        }

        return $finder->fetch();
    }

    /**
     * @return FakerGenerator
     */
    public function faker(): FakerGenerator
    {
        if ($this->faker === null)
        {
            $this->faker = FakerFactory::create();
        }

        return $this->faker;
    }

    /**
     * @param $class
     *
     * @return AbstractService
     */
    protected function service(string $class) : AbstractService
    {
        return call_user_func_array([$this->app(), 'service'], func_get_args());
    }

    /**
     * @param string $identifier
     *
     * @return Repository
     */
    protected function repository(string $identifier): Repository
    {
        return $this->app()->repository($identifier);
    }

    /**
     * @param string $identifier
     *
     * @return Finder
     */
    protected function finder(string $identifier): Finder
    {
        return $this->app()->finder($identifier);
    }

    /**
     * @return ArrayObject
     */
    protected function options() : ArrayObject
    {
        return $this->app()->options();
    }

    /**
     * @param string|null $key
     *
     * @return mixed
     */
    protected function config(string $key = null)
    {
        return $this->app()->config($key);
    }

    /**
     * @return EntityManager
     */
    protected function em() : EntityManager
    {
        return $this->app()->em();
    }

    /**
     * @return BaseApp
     */
    protected function app() : BaseApp
    {
        return $this->app;
    }
}