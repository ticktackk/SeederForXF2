<?php

namespace TickTackk\Seeder\Job;

use XF\Job\AbstractJob;
use XF\Mvc\Entity\Repository;
use TickTackk\Seeder\Repository\Seed as SeedRepo;
use XF\Job\JobResult;
use function is_array;

/**
 * Class Seed
 *
 * @package TickTackk\Seeder\Job
 */
class Seed extends AbstractJob
{
    /**
     * @var array
     */
    protected $defaultData = [
        'current_seed' => null,
        'seeds' => [],
        'done' => 0,
        'limit' => 100,
        'bulk_limits' => null
    ];

    /**
     * @var string
     */
    protected $currentSeedTitle;

    /**
     * @param int $maxRunTime
     *
     * @return JobResult
     * @throws \Exception
     */
    public function run($maxRunTime): JobResult
    {
        $startTime = microtime(true);
        $seedRepo = $this->getSeedRepo();

        if (!is_array($this->data['seeds']))
        {
            $this->data['seeds'] = $this->data['current_seed'] ? [$this->data['type']] : $seedRepo->getAvailableSeeds();
            $this->data['current_seed'] = null;
            if (is_array($this->data['bulk_limits']))
            {
                $this->data['limit'] = null;
            }
        }

        if (!$this->data['current_seed'])
        {
            $this->data['current_seed'] = array_shift($this->data['seeds']);
            if (!$this->data['current_seed'])
            {
                return $this->complete();
            }

            if (is_array($this->data['bulk_limits']))
            {
                $this->data['limit'] = $this->data['bulk_limits'][$this->data['current_seed']] ?? null;
            }

            if (!$this->data['limit'])
            {
                $this->data['current_seed'] = null;
                return $this->resume();
            }
            $this->data['done'] = 0;
        }

        $currentSeed = $this->data['current_seed'];
        $limit = $this->data['limit'];

        if (!$seedRepo->isValidSeed($currentSeed))
        {
            $this->data['current_seed'] = null;

            if (is_array($this->data['bulk_limits']))
            {
                $this->data['limit'] = null;
            }
            return $this->resume();
        }

        $seedHandler = $seedRepo->getSeedHandler($currentSeed, false);
        if (!$seedHandler)
        {
            $this->data['current_seed'] = null;

            if (is_array($this->data['bulk_limits']))
            {
                $this->data['limit'] = null;
            }
            return $this->resume();
        }

        $this->currentSeedTitle = $seedHandler->getTitle();

        /**
         * @return bool
         */
        $hasMore = function () use($limit)
        {
            return $this->data['done'] < $limit;
        };

        do
        {
            $seedHandler->seed($errors);

            if ($errors)
            {
                foreach ($errors AS $error)
                {
                    \XF::logException($error);
                }
            }
            else
            {
                $this->data['done']++;
            }

            $this->saveIncrementalData();

            // we need to throw away the previous entity cache to prevent potential memory issues
            $this->app->em()->clearEntityCache();

            if (!$hasMore())
            {
                $seedHandler->postSeed();

                $this->data['current_seed'] = null;
                return $this->resume();
            }

            $timeRemaining = $maxRunTime - (microtime(true) - $startTime);
            $errors = [];
        }
        while ($hasMore() && $timeRemaining >= 0.5);

        return $this->resume();
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getStatusMessage(): string
    {
        $actionPhrase = \XF::phrase('tckSeeder_seeding');

        return sprintf('%s... %s (%s/%s)',
            $actionPhrase,
            $this->currentSeedTitle,
            $this->app->language()->numberFormat($this->data['done']),
            $this->app->language()->numberFormat($this->data['limit'])
        );
    }

    /**
     * @return bool
     */
    public function canTriggerByChoice(): bool
    {
        return false;
    }

    /**
     * @return bool
     */
    public function canCancel(): bool
    {
        return true;
    }

    /**
     * @return Repository|SeedRepo
     */
    protected function getSeedRepo() : SeedRepo
    {
        return $this->app->repository('TickTackk\Seeder:Seed');
    }
}