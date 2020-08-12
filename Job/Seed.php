<?php

namespace TickTackk\Seeder\Job;

use TickTackk\Seeder\Job\Exception\InvalidContentTypePluralProvidedException;
use TickTackk\Seeder\Job\Exception\InvalidSeedClassProvidedException;
use TickTackk\Seeder\Seed\AbstractSeed;
use XF\App as BaseApp;
use XF\Job\JobResult;
use XF\Job\Manager as JobManager;
use XF\Mvc\Entity\Repository;
use XF\Mvc\Entity\Finder;
use XF\Db\AbstractAdapter as DbAdapter;
use XF\Job\AbstractJob;
use XF\Phrase;
use XF\Service\AbstractService;
use XF\Mvc\Entity\Manager as EntityManager;

class Seed extends AbstractJob
{
    protected $defaultData = [
        'steps' => null,
        'done' => null,
        'batch' => null,
        'limit' => null,
        'seed_class' => null,
        'seed_params' => null,
        'content_type_plural' => null
    ];

    protected function getFromData(string $key, $fallback = null)
    {
        return $this->getData()[$key] ?? $fallback;
    }

    protected function getSeedClass() :? string
    {
        return $this->getFromData('seed_class');
    }

    protected function getSeedParams() :? array
    {
        return $this->getFromData('seed_params');
    }

    protected function getStepCount() :? int
    {
        return $this->getFromData('step');
    }

    protected function getDoneCount() :? int
    {
        return $this->getFromData('done');
    }

    protected function getLimit() :? int
    {
        return $this->getFromData('limit');
    }

    protected function getContentTypePlural() :? string
    {
        return $this->getFromData('content_type_plural');
    }

    protected function setupData(array $data) : array
    {
        $placeholders = [
            'steps' => 0,
            'done' => 0,
            'batch' => 100,
            'limit' => 500,
            'seed_params' => []
        ];

        foreach ($placeholders AS $key => $fallback)
        {
            if (!\array_key_exists($key, $data))
            {
                $data[$key] = null;
            }

            if ($data[$key] === null)
            {
                $data[$key] = $fallback;
            }
        }

        if ($data['content_type_plural'] === null)
        {
            throw new InvalidContentTypePluralProvidedException($data['content_type_plural']);
        }

        return parent::setupData($data);
    }

    protected function increaseStepCount() : void
    {
        $this->data['steps']++;
    }

    protected function increaseDoneCount() : void
    {
        $this->data['done']++;
    }

    protected function hasSeededAll() : bool
    {
        return $this->getDoneCount() === $this->getLimit();
    }

    /**
     * @param int $maxRunTime
     *
     * @return JobResult
     *
     * @throws \Exception
     */
    public function run($maxRunTime) : JobResult
    {
        $startTime = \microtime(true);

        $this->increaseStepCount();

        if ($this->hasSeededAll())
        {
            return $this->complete();
        }

        $doneNow = 0;
        do
        {
            if ($this->hasSeededAll())
            {
                break;
            }

            if ($this->seed()->insert($this->getSeedParams()))
            {
                $this->increaseDoneCount();
                $doneNow++;
            }

            $timeRemaining = $maxRunTime - (microtime(true) - $startTime);
        }
        while ($timeRemaining >= 0.5);

        return $this->resume();
    }

    public function getStatusMessage() : string
    {
        $seeding = \XF::phrase('tckSeeder_seeding');
        $type = $this->getContentTypePlural();

        return \sprintf(
            '%s... %s (%s/%s)',
            $seeding, $type,
            $this->getDoneCount(), $this->getLimit()
        );
    }

    public function canCancel() : bool
    {
        return false;
    }

    public function canTriggerByChoice() : bool
    {
        return true;
    }

    protected function app() : BaseApp
    {
        return $this->app;
    }

    protected function repository(string $identifier) : Repository
    {
        return $this->app()->repository($identifier);
    }

    protected function finder(string $identifier) : Finder
    {
        return $this->app()->finder($identifier);
    }

    protected function service(string $identifier, ...$arguments) : AbstractService
    {
        return $this->app()->service($identifier, ...$arguments);
    }

    protected function db() : DbAdapter
    {
        return $this->app()->db();
    }

    protected function jobManager() : JobManager
    {
        return $this->app()->jobManager();
    }

    protected function em() : EntityManager
    {
        return $this->app()->em();
    }

    /**
     * @return AbstractSeed|null
     *
     * @throws \Exception
     */
    protected function seed() :? AbstractSeed
    {
        $class = $this->getSeedClass();
        if (!$class || empty($class))
        {
            throw new InvalidSeedClassProvidedException($class);
        }

        $class = \XF::stringToClass($class, '\%s\Seed\%s');
        $class = \XF::extendClass($class);

        if (!\class_exists($class))
        {
            throw new InvalidSeedClassProvidedException($class);
        }

        return new $class($this->app());
    }
}