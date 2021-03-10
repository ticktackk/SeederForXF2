<?php

namespace TickTackk\Seeder\Seed;

/**
 * @since 1.1.0 Release Candidate 1
 */
class ThreadPrefixGroup extends AbstractContentPrefixGroup
{
    protected function getClassIdentifier(): string
    {
        return 'XF:ThreadPrefixGroup';
    }
}