<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Entity;
use XF\Mvc\Entity\Finder;
use XFMG\Entity\Category as CategoryEntity;
use XFMG\Entity\Album as AlbumEntity;
use XFMG\Entity\MediaItem;
use XFMG\Finder\Album as AlbumFinder;
use XFMG\Finder\MediaItem as MediaItemFinder;

/**
 * @method Finder finderWithRandomOrder(string $identifier)
 */
trait MediaGalleryRandomContentTrait
{
    public function findRandomCategory() :? CategoryEntity
    {
        return $this->finderWithRandomOrder('XFMG:Category')
            ->where('category_type', ['album', 'media'])
            ->fetchOne();
    }

    public function findRandomAlbum() :? AlbumEntity
    {
        /** @var AlbumFinder $finder */
        $finder = $this->finderWithRandomOrder('XFMG:Album');

        return $finder->applyVisibilityLimit()->applyAddMediaLimit()->fetchOne();
    }

    protected function findRandomMediaItem() :? MediaItem
    {
        return $this->finderWithRandomOrder('XFMG:MediaItem')->fetchOne();
    }

    /**
     * @return AlbumEntity|MediaItem|null
     */
    protected function findRandomAlbumOrMediaItemContent() :? Entity
    {
        do
        {
            $content = $this->faker()->boolean
                ? $this->findRandomAlbum()
                : $this->findRandomMediaItem();
        }
        while ($content === null);

        return $content;
    }
}