<?php

namespace TickTackk\Seeder\Seed;

use XF\Finder\User as UserFinder;
use XFRM\Entity\ResourceItem as ResourceItemEntity;
use XFRM\Service\ResourceItem\CreateVersionUpdate as ResourceVersionUpdateCreatorSvc;

class ResourceManagerUpdate extends AbstractSeed
{
    use RandomVersionGeneratorTrait;

    protected function setupVisitorFinder(UserFinder $userFinder): UserFinder
    {
        $userFinder->where('xfrm_resource_count', '>', 0);

        return $userFinder;
    }

    /**
     * @throws \Exception
     */
    protected function seed(array $params = []): bool
    {
        $resource = $this->findRandomResourceByVisitor();
        if (!$resource)
        {
            return false;
        }

        $faker = $this->faker();
        $contextParams = ['resource_id' => $resource->resource_id];

        $resourceVersionUpdateCreatorSvc = $this->getResourceVersionUpdateCreatorSvc($resource);
        $resourceVersionUpdateCreatorSvc->getUpdateCreator()->setTitle(
            \utf8_substr($faker->words($faker->numberBetween(1, 15), true), 0, 100)
        );
        $resourceVersionUpdateCreatorSvc->getUpdateCreator()->setMessage(
            $faker->paragraphs($faker->numberBetween(1, 3), true)
        );

        if ($faker->boolean)
        {
            $resourceVersionUpdateCreatorSvc->getUpdateCreator()->logIp($faker->boolean ? $faker->ipv4 : $faker->ipv6);
        }
        else
        {
            $resourceVersionUpdateCreatorSvc->getUpdateCreator()->logIp(false);
        }

        $createVersionAndUpdate = $faker->boolean;
        $createVersionOnly = $faker->boolean;
        $createUpdateOnly = $faker->boolean;

        if ($createVersionAndUpdate || $createVersionOnly)
        {
            $resourceVersionCreatorSvc = $resourceVersionUpdateCreatorSvc->getVersionCreator();
            $resourceVersionCreatorSvc->setVersionString($this->getRandomVersionString(), true);

            if ($resource->isDownloadable())
            {
                $category = $resource->Category;

                if ($faker->boolean)
                {
                    if ($category->allow_local || $resource->getResourceTypeDetailed() == 'download_local')
                    {
                        $attachment = $this->insertAttachmentFromUrl($faker->imageUrl(), 'resource_version', $contextParams);
                        if (!$attachment)
                        {
                            return false;
                        }

                        $resourceVersionCreatorSvc->setAttachmentHash($attachment->temp_hash);
                    }
                }
                else
                {
                    if ($category->allow_external || $resource->getResourceTypeDetailed() == 'download_external')
                    {
                        $resourceVersionCreatorSvc->setDownloadUrl(
                            $faker->url . '/' . \XF::generateRandomString(10) . '.' . $faker->fileExtension
                        );
                    }
                }
            }

            if ($resource->isExternalPurchasable())
            {
                $purchaseFields = [
                    'price' => $resource->price,
                    'currency' => $resource->currency,
                    'external_purchase_url' => $resource->external_purchase_url
                ];

                if ($faker->boolean)
                {
                    $purchaseFields['price'] = $this->getRandomCostAmount(99999999);
                }

                if ($faker->boolean)
                {
                    $purchaseFields['currency'] = $faker->currencyCode;
                }

                if ($faker->boolean)
                {
                    $purchaseFields['external_purchase_url'] = $faker->url;
                }

                $resourceVersionUpdateCreatorSvc->addResourceChanges($purchaseFields);
            }
        }

        if ($createVersionAndUpdate || $createUpdateOnly)
        {
            $resourceUpdateCreatorSvc = $resourceVersionUpdateCreatorSvc->getUpdateCreator();

            $resourceUpdateCreatorSvc->setMessage($faker->text);
            $resourceUpdateCreatorSvc->setTitle(
                \utf8_substr($faker->words($faker->numberBetween(1, 15), true), 0, 100)
            );

            if ($faker->boolean)
            {
                $attachmentHash = $this->generateAttachmentHash();
                $randomImagesCount = $faker->numberBetween(1, 5);
                $imageAttachmentsCreated = 0;

                do
                {
                    $attachment = $this->insertAttachmentFromUrl(
                        $faker->imageUrl(),
                        'resource_update',
                        $contextParams,
                        $attachmentHash
                    );
                    if ($attachment)
                    {
                        $imageAttachmentsCreated++;
                    }
                }
                while ($imageAttachmentsCreated !== $randomImagesCount);

                $resourceUpdateCreatorSvc->setAttachmentHash($attachmentHash);
            }
        }

        if (!$resourceVersionUpdateCreatorSvc->validate($errors))
        {
            return false;
        }

        $resourceVersionUpdateCreatorSvc->save();

        return true;
    }

    protected function findRandomResourceByVisitor() :? ResourceItemEntity
    {
        return $this->finderWithRandomOrder('XFRM:ResourceItem')
            ->where('user_id', \XF::visitor()->user_id)
            ->fetchOne();
    }

    protected function getResourceVersionUpdateCreatorSvc(ResourceItemEntity $resourceItem) : ResourceVersionUpdateCreatorSvc
    {
        return $this->service('XFRM:ResourceItem\CreateVersionUpdate', $resourceItem);
    }
}