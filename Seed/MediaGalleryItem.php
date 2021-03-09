<?php

namespace TickTackk\Seeder\Seed;

use Bluemmb\Faker\PicsumPhotosProvider as PicsumFakerProvider;
use Faker\Generator;
use Faker\Provider\Lorem;
use Faker\Provider\Youtube as YouTubeFakerProvider;
use TickTackk\Seeder\Seed\Exception\DownloadUrlDidNotReturnOkResponseException;
use XF\Mvc\Entity\Entity;
use XF\Repository\BbCodeMediaSite as BbCodeMediaSiteRepo;
use XFMG\Entity\Album as AlbumEntity;
use XFMG\Entity\Category as CategoryEntity;
use XFMG\Entity\MediaTemp as MediaTempEntity;
use XFMG\Service\Media\Creator as MediaCreatorSvc;
use XFMG\Service\Media\TempCreator as MediaTempCreatorSvc;

class MediaGalleryItem extends AbstractSeed
{
    use MediaGalleryRandomContentTrait;

    /**
     * @throws \XF\PrintableException
     * @throws \Exception
     */
    protected function seed(array $params = []): bool
    {
        /** @var Generator|PicsumFakerProvider|YouTubeFakerProvider $faker */
        $faker = $this->faker();
        $container = null;

        do
        {
            $container = $faker->boolean ? $this->findRandomAlbum() : $this->findRandomCategory();
        }
        while ($container === null);

        $contextParams = [$container->structure()->primaryKey => $container->getEntityId()];
        $mediaCreatorSvc = null;

        do
        {
            switch ($this->getRandomMediaType($container))
            {
                case 'image':
                    try
                    {
                        $attachment = $this->insertAttachmentFromUrl(
                            $faker->imageUrl(),
                            'xfmg_media',
                            $contextParams
                        );
                        if (!$attachment)
                        {
                            return false;
                        }

                        $mediaTemp = $this->findMediaTempByAttachmentId($attachment->attachment_id);
                        $mediaCreatorSvc = $this->getMediaCreatorSvc($mediaTemp);
                        $mediaCreatorSvc->setAttachment($attachment->attachment_id, $attachment->temp_hash);
                    }
                    catch (DownloadUrlDidNotReturnOkResponseException $e)
                    {
                        return false;
                    }
                    break;

                case 'embed':
                    $mediaTempCreatorSvc = $this->getMediaTempCreatorSvc();

                    $match = $this->getRandomBbCodeMediaMatch();
                    $mediaTempCreatorSvc->setMediaSite(
                        $match['url'],
                        $match['media_site_id'],
                        $match['media_id']
                    );

                    if ($mediaTempCreatorSvc->validate())
                    {
                        $mediaTemp = $mediaTempCreatorSvc->save();
                        $mediaCreatorSvc = $this->getMediaCreatorSvc($mediaTemp);
                    }
                    break;
            }
        }
        while ($mediaCreatorSvc === null);

        $mediaCreatorSvc->setContainer($container);

        $title = Lorem::sentence();
        $description = '';
        if ($faker->boolean)
        {
            $description = $faker->text;
        }
        $mediaCreatorSvc->setTitle($title, $description);

        if ($faker->boolean)
        {
            $mediaCreatorSvc->setTags($faker->words($faker->numberBetween(3, 10)));
        }

        if (!$mediaCreatorSvc->validate($errors))
        {
            return false;
        }

        $mediaCreatorSvc->save();

        return true;
    }

    protected function getRandomBbCodeMediaMatch() : array
    {
        /** @var Generator|YouTubeFakerProvider $faker */
        $faker = $this->faker();
        $bbCodeMediaSiteRepo = $this->getBbCodeMediaSiteRepo();
        $bbCodeMediaSites = $bbCodeMediaSiteRepo->findBbCodeMediaSitesForList()->fetch();

        $match = null;
        do
        {
            $url = $faker->youtubeUri();
            $match = $bbCodeMediaSiteRepo->urlMatchesMediaSiteList($url, $bbCodeMediaSites);
        }
        while (!\is_array($match));

        return \array_merge($match, [
            'url' => $url
        ]);
    }

    protected function getRandomMediaType(Entity $container) : string
    {
        $faker = $this->faker();

        if ($container instanceof CategoryEntity)
        {
            $allowedTypes = $container->allowed_types;
            foreach ($allowedTypes AS $allowedType)
            {
                if ($faker->boolean && \in_array($allowedType, ['image', 'embed']))
                {
                    return $allowedType;
                }
            }

            return $this->getRandomMediaType($container);
        }
        else if ($container instanceof AlbumEntity && $container->Category)
        {
            return $this->getRandomMediaType($container->Category);
        }

        if ($faker->boolean)
        {
            return 'embed';
        }

        return 'image';
    }

    protected function findMediaTempByAttachmentId(int $attachmentId) :? MediaTempEntity
    {
        return $this->finder('XFMG:MediaTemp')->where('attachment_id', $attachmentId)->fetchOne();
    }

    protected function getMediaTempCreatorSvc() : MediaTempCreatorSvc
    {
        return $this->service('XFMG:Media\TempCreator');
    }

    protected function getMediaCreatorSvc(MediaTempEntity $mediaTemp) : MediaCreatorSvc
    {
        return $this->service('XFMG:Media\Creator', $mediaTemp);
    }

    protected function getBbCodeMediaSiteRepo() : BbCodeMediaSiteRepo
    {
        return $this->repository('XF:BbCodeMediaSite');
    }
}