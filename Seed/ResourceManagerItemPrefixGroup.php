<?php

namespace TickTackk\Seeder\Seed;

/**
 * @since 1.1.0 Release Candidate 1
 */
class ResourceManagerItemPrefixGroup extends AbstractContentPrefixGroup
{
    protected function getClassIdentifier(): string
    {
        return 'XFRM:ResourcePrefixGroup';
    }
}