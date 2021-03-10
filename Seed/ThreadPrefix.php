<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XF\Entity\AbstractPrefix as AbstractPrefixEntity;
use XF\Mvc\FormAction;
use XF\Repository\ThreadWatch as ThreadWatchRepo;
use XF\Service\Thread\Creator as ThreadCreatorSvc;
use XF\Repository\Thread as ThreadRepo;

/**
 * @since 1.1.0 Release Candidate 1
 */
class ThreadPrefix extends AbstractContentPrefix
{
    protected function getClassIdentifier(): string
    {
        return 'XF:ThreadPrefix';
    }

    protected function getGroupClassIdentifier(): string
    {
        return 'XF:ThreadPrefixGroup';
    }

    protected function getMapIdentifier(): string
    {
        return 'XF:ForumPrefix';
    }

    protected function getContainerIdentifier(): string
    {
        return 'XF:Forum';
    }
}