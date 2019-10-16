<?php

namespace TickTackk\Seeder\Job;

use XF\Job\AbstractJob;

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
        'seeds' => [],
        'currentSeed' => null,
        'seedStats' => []
    ];

    /**
     * @param int $maxRunTime
     *
     * @return \XF\Job\JobResult
     * @throws \XF\PrintableException
     * @throws \Exception
     */
    public function run($maxRunTime): \XF\Job\JobResult
    {
        $startTime = microtime(true);

        if ($this->data['currentSeed'] === null)
        {
            $this->data['currentSeed'] = reset($this->data['seeds']);
        }

        $currentSeedClass = $this->getCurrentSeedClass();
        if (isset($this->data['seedStats'][$currentSeedClass]) && $this->getDoneForCurrentSeed() >= $this->getLimitForCurrentSeed())
        {
            $currentSeedClass = $this->getNextSeed();
            if ($currentSeedClass === null)
            {
                return $this->complete();
            }

            $this->data['currentSeed'] = $currentSeedClass;
        }

        $currentSeed = $this->getCurrentSeed();
        if (!isset($this->data['seedStats'][$currentSeedClass]))
        {
            $this->data['seedStats'][$currentSeedClass] = [
                'done' => 0,
                'limit' => $currentSeed->getLimit()
            ];
        }

        do
        {
            $currentSeed->run();
            $this->bumpDoneForCurrentSeed();

            $hasSeededAll = $this->getDoneForCurrentSeed() >= $this->getLimitForCurrentSeed();
            $timeRemaining = $maxRunTime - (microtime(true) - $startTime);
        }
        while (!$hasSeededAll && $timeRemaining >= 1);

        return $this->resume();
    }

    protected function bumpDoneForCurrentSeed(): void
    {
        $this->data['seedStats'][$this->getCurrentSeedClass()]['done']++;
    }

    /**
     * @param string $seedName
     *
     * @return \TickTackk\Seeder\Seed\AbstractSeed
     * @throws \Exception
     */
    public function getSeed(string $seedName) : \TickTackk\Seeder\Seed\AbstractSeed
    {
        /** @var \TickTackk\Seeder\Repository\Seed $seedRepo */
        $seedRepo = $this->app->repository('TickTackk\Seeder:Seed');
        return $seedRepo->getSeedHandler($seedName);
    }

    /**
     * @return \TickTackk\Seeder\Seed\AbstractSeed
     * @throws \Exception
     */
    public function getCurrentSeed() : \TickTackk\Seeder\Seed\AbstractSeed
    {
        return $this->getSeed($this->getCurrentSeedClass());
    }

    /**
     * @return string
     */
    protected function getCurrentSeedClass(): string
    {
        return $this->data['currentSeed'] ?? '';
    }

    /**
     * @return string
     * @throws \Exception
     */
    protected function getTitleForCurrentSeed() : string
    {
        $currentSeed = $this->getCurrentSeed();

        $title = $currentSeed->getTitle();
        if ($title instanceof \XF\Phrase)
        {
            $title = $title->render('raw');
        }

        return $title;
    }

    /**
     * @return int
     * @throws \Exception
     */
    protected function getDoneForCurrentSeed(): int
    {
        $done = $this->data['seedStats'][$this->getCurrentSeedClass()]['done'] ?? 0;

        $currentSeed = $this->getCurrentSeed();
        $currentSeed->setDone($done);

        return $currentSeed->getDone();
    }

    /**
     * @return int
     * @throws \Exception
     */
    public function getLimitForCurrentSeed(): int
    {
        $limit = $this->data['seedStats'][$this->getCurrentSeedClass()]['limit'] ?? 0;

        $currentSeed = $this->getCurrentSeed();
        $currentSeed->setLimit($limit);

        return $currentSeed->getLimit();
    }

    /**
     * @return null|string
     */
    protected function getNextSeed(): ?string
    {
        $seedNameIndexes = array_values($this->data['seeds']);
        $position = array_search($this->data['currentSeed'], $seedNameIndexes, true);
        return !empty($seedNameIndexes[$position + 1]) ? $seedNameIndexes[$position + 1] : null;
    }

    /**
     * @return string
     * @throws \Exception
     */
    public function getStatusMessage(): string
    {
        return sprintf(
            '%s %s (%d/%d)...',
            'Seeding...',
            $this->getTitleForCurrentSeed(),
            $this->getDoneForCurrentSeed(),
            $this->getLimitForCurrentSeed()
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
}