<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Entity;
use XFMG\Service\Comment\Creator as CommentCreatorSvc;

class MediaGalleryComment extends AbstractSeed
{
    use MediaGalleryRandomContentTrait;

    protected function seed(array $params = []): bool
    {
        $randomContent = $this->findRandomAlbumOrMediaItemContent();
        if (!$randomContent)
        {
            return false;
        }

        $faker = $this->faker();
        $commentCreatorSvc = $this->getCommentCreatorSvc($randomContent);
        $commentCreatorSvc->setMessage($faker->text);

        if (!$commentCreatorSvc->validate())
        {
            return false;
        }

        $commentCreatorSvc->save();

        return true;
    }

    protected function getCommentCreatorSvc(Entity $content) : CommentCreatorSvc
    {
        return $this->service('XFMG:Comment\Creator', $content);
    }
}