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
class ResourceManagerItemPrefix extends AbstractContentPrefix
{
    protected function getClassIdentifier(): string
    {
        return 'XFRM:ResourcePrefix';
    }

    protected function getGroupClassIdentifier(): string
    {
        return 'XFRM:ResourcePrefixGroup';
    }

    protected function getMapIdentifier(): string
    {
        return 'XFRM:CategoryPrefix';
    }

    protected function getContainerIdentifier(): string
    {
        return 'XFRM:Category';
    }
}