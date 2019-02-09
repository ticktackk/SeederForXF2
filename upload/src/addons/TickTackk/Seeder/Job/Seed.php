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
        'currentSeedPhrase' => null,
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

        $currentSeed = $this->getCurrentSeed();
        if (isset($this->data['seedStats'][$currentSeed]))
        {
            if ($this->getDoneForCurrentSeed() >= $this->getLimitForCurrentSeed())
            {
                $currentSeed = $this->getNextSeed();
                if ($currentSeed === null)
                {
                    return $this->complete();
                }

                $this->data['currentSeed'] = $currentSeed;
            }
        }

        /** @var \TickTackk\Seeder\Repository\Seed $seedRepo */
        $seedRepo = $this->app->repository('TickTackk\Seeder:Seed');
        if ($seedHandler = $seedRepo->getSeedHandler($currentSeed))
        {
            $this->data['currentSeedPhrase'] = $seedHandler->getContentTypePhrased(true)->render('raw');

            if (!isset($this->data['seedStats'][$currentSeed]))
            {
                $this->data['seedStats'][$currentSeed] = [
                    'done' => 0,
                    'limit' => $seedHandler->getLimit()
                ];
            }

            do
            {
                $seedHandler->run();
                $this->bumpDoneForCurrentSeed();

                $hasSeededAll = $this->getDoneForCurrentSeed() >= $this->getLimitForCurrentSeed();
                $timeRemaining = $maxRunTime - (microtime(true) - $startTime);
            }
            while (!$hasSeededAll && $timeRemaining >= 1);
        }

        return $this->resume();
    }

    protected function bumpDoneForCurrentSeed(): void
    {
        $this->data['seedStats'][$this->getCurrentSeed()]['done']++;
    }

    /**
     * @return string
     */
    protected function getCurrentSeed(): string
    {
        return $this->data['currentSeed'] ?? '';
    }

    /**
     * @return int
     */
    protected function getDoneForCurrentSeed(): int
    {
        return $this->data['seedStats'][$this->getCurrentSeed()]['done'] ?? 0;
    }

    /**
     * @return int
     */
    public function getLimitForCurrentSeed(): int
    {
        return $this->data['seedStats'][$this->getCurrentSeed()]['limit'] ?? 0;
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
     */
    public function getStatusMessage(): string
    {
        return sprintf(
            '%s %s (%d/%d)...',
            'Seeding...',
            $this->data['currentSeedPhrase'],
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