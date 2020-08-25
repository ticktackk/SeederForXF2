<?php

namespace TickTackk\Seeder\Seed;

use Faker\Provider\Lorem;
use XFMG\Service\Album\Creator as AlbumCreatorSvc;

class MediaGalleryAlbum extends AbstractSeed
{
    use MediaGalleryRandomContainerTrait;

    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();
        $albumCreatorSvc = $this->getAlbumCreatorSvc();

        if ($faker->boolean)
        {
            $randomCategory = $this->findRandomCategory();
            if (!$randomCategory)
            {
                return false;
            }
        }

        $title = Lorem::sentence();
        $description = '';
        if ($faker->boolean)
        {
            $description = $faker->text;
        }
        $albumCreatorSvc->setTitle($title, $description);

        $albumCreatorSvc->setViewPrivacy($this->getRandomViewPrivacy(), $this->getRandomUserIds());
        $albumCreatorSvc->setAddPrivacy($this->getRandomViewPrivacy(), $this->getRandomUserIds());

        if (!$albumCreatorSvc->validate($errors))
        {
            return false;
        }

        $albumCreatorSvc->save();

        return true;
    }

    protected function getRandomViewPrivacy() : string
    {
        $privacyValue = null;

        do
        {
            $faker = $this->faker();

            foreach (['private', 'members', 'public', 'shared'] AS $randomPrivacyValue)
            {
                if ($faker->boolean)
                {
                    $privacyValue = $randomPrivacyValue;
                }
            }
        }
        while ($privacyValue === null);

        return $privacyValue;
    }

    protected function getRandomUserIds(int $limit = null) : array
    {
        return $this->finderWithRandomOrder('XF:User')->limit($limit ?: $this->faker()->numberBetween(3, 10))->fetch()->keys();
    }

    protected function getAlbumCreatorSvc() : AlbumCreatorSvc
    {
        return $this->service('XFMG:Album\Creator');
    }
}