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
     * @var string
     */
    protected $contentType;

    /**
     * @var \Faker\Generator
     */
    protected $faker;

    /**
     * AbstractSeed constructor.
     *
     * @param \XF\App $app
     * @param string  $contentType
     */
    public function __construct(\XF\App $app, string $contentType)
    {
        $this->app = $app;

        $this->setContentType($contentType);
    }

    /**
     * @param string $contentType
     */
    protected function setContentType(string $contentType) : void
    {
        $this->contentType = $contentType;
    }

    /**
     * @return string
     */
    public function getContentType() : string
    {
        return $this->contentType;
    }

    /**
     * @param bool $plural
     *
     * @return \XF\Phrase
     */
    public function getContentTypePhrased(bool $plural = false) : \XF\Phrase
    {
        return $this->app->getContentTypePhrase($this->getContentType(), $plural);
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
     * @return int
     */
    abstract public function getRunOrder() : int;

    /**
     * @return int
     */
    abstract public function getLimit(): int;

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
     *
     * @return null|Entity
     */
    protected function randomEntity(string $identifier) :? Entity
    {
        $randomEntities = $this->randomEntities($identifier, 1);
        if ($randomEntities->count())
        {
            return $randomEntities->first();
        }

        return null;
    }

    /**
     * @param string      $identifier
     * @param int         $limit
     * @param string|null $orderBy
     *
     * @return ArrayCollection
     */
    protected function randomEntities(string $identifier, int $limit, string $orderBy = null) : ArrayCollection
    {
        return $this->finder($identifier)
            ->order($orderBy ?: Finder::ORDER_RANDOM)
            ->limit($limit)
            ->fetch();
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
}