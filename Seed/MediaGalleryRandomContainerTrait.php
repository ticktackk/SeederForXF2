<?php

namespace TickTackk\Seeder\Seed;

use XF\Mvc\Entity\Finder;
use XFMG\Entity\Category as CategoryEntity;
use XFMG\Entity\Album as AlbumEntity;
use XFMG\Finder\Album as AlbumFinder;

/**
 * @method Finder finderWithRandomOrder(string $identifier)
 */
trait MediaGalleryRandomContainerTrait
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
}