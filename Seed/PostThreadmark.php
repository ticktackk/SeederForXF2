<?php

namespace TickTackk\Seeder\Seed;

use SV\Threadmarks\Entity\ThreadmarkContainerInterface as ThreadmarkContainerEntityInterface;
use SV\Threadmarks\Entity\ThreadmarkContentInterface as ThreadmarkContentEntityInterface;
use XF\Mvc\Entity\Entity;
use SV\Threadmarks\XF\Entity\Post as ExtendedPostEntityFromThreadmarks;

class PostThreadmark extends AbstractContentThreadmark
{
    /**
     * @inheritDoc
     */
    protected function findRandomContentAndContainer() :? array
    {
        /** @var ExtendedPostEntityFromThreadmarks $post */
        $post = $this->finderWithRandomOrder('XF:Post')
            ->where('Threadmark.threadmark_id', null)
            ->with('Thread', true)
            ->fetchOne();
        if (!$post)
        {
            return null;
        }

        return [
            $post,
            $post->Thread
        ];
    }
}