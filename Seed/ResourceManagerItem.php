<?php

namespace TickTackk\Seeder\Seed;

use XF\Phrase;
use XFRM\Entity\Category as CategoryEntity;
use XFRM\Service\ResourceItem\Create as ResourceItemCreatorSvc;

class ResourceManagerItem extends AbstractSeed
{
    use RandomVersionGeneratorTrait;

    protected function seed(array $params = []): bool
    {
        $randomCategory = $this->findRandomCategory();
        if (!$randomCategory)
        {
            return false;
        }

        $faker = $this->faker();
        $contextParams = [
            'resource_category_id' => $randomCategory->getEntityId()
        ];

        $resourceItemCreatorSvc = $this->getResourceCreatorSvc($randomCategory);
        $resourceItemCreatorSvc->setContent(
            \utf8_substr($faker->words($faker->numberBetween(1, 15), true), 0, 100),
            $faker->paragraphs($faker->numberBetween(1, 3), true),
            $faker->boolean
        );
        $resourceItemCreatorSvc->getResource()->tag_line = \utf8_substr(
            $faker->words($faker->numberBetween(1, 25), true), 0, 100
        );

        if ($faker->boolean)
        {
            $resourceItemCreatorSvc->getResource()->external_url = $faker->url;
        }

        if ($faker->boolean)
        {
            $resourceItemCreatorSvc->getResource()->alt_support_url = $faker->url;
        }

        if ($faker->boolean)
        {
            $resourceItemCreatorSvc->logIp($faker->boolean ? $faker->ipv4 : $faker->ipv6);
        }
        else
        {
            $resourceItemCreatorSvc->logIp(false);
        }

        if ($faker->boolean)
        {
            $resourceItemCreatorSvc->setTags($faker->words($faker->numberBetween(3, 10)));
        }

        $resourceItemCreatorSvc->setVersionString($this->getRandomVersionString());

        switch ($this->getRandomResourceType($randomCategory))
        {
            case 'download_local':
                $attachment = $this->insertAttachmentFromUrl($faker->imageUrl(), 'resource_version', $contextParams);
                if (!$attachment)
                {
                    return false;
                }

                $resourceItemCreatorSvc->setLocalDownload($attachment->temp_hash);
                break;

            case 'download_external':
                $resourceItemCreatorSvc->setExternalDownload(
                    $faker->url . '/' . \XF::generateRandomString(10) . '.' . $faker->fileExtension
                );
                break;

            case 'external_purchase':
                $resourceItemCreatorSvc->setExternalPurchasable(
                    $this->getRandomCostAmount(99999999),
                    $faker->currencyCode,
                    $faker->url
                );
                break;

            case 'fileless':
                $resourceItemCreatorSvc->setFileless();
                break;

            default:
                return false;
        }

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

            $resourceItemCreatorSvc->setDescriptionAttachmentHash($attachmentHash);
        }

        if (!$resourceItemCreatorSvc->validate($errors))
        {
            return false;
        }

        $resourceItemCreatorSvc->save();

        return true;
    }

    protected function getAllowedResourceTypes(CategoryEntity $category) : array
    {
        $allowedTypes = [];

        if ($category->allow_local)
        {
            $allowedTypes[] = 'download_local';
        }

        if ($category->allow_external)
        {
            $allowedTypes[] = 'download_external';
        }

        if ($category->allow_commercial_external)
        {
            $allowedTypes[] = 'external_purchase';
        }

        if ($category->allow_fileless)
        {
            $allowedTypes[] = 'fileless';
        }

        return $allowedTypes;
    }

    protected function getRandomResourceType(CategoryEntity $category) : string
    {
        foreach ($this->getAllowedResourceTypes($category) AS $allowedResourceType)
        {
            if ($this->faker()->boolean)
            {
                return $allowedResourceType;
            }
        }

        return $this->getRandomResourceType($category);
    }

    protected function findRandomCategory() :? CategoryEntity
    {
        return $this->finderWithRandomOrder('XFRM:Category')->fetchOne();
    }

    protected function getResourceCreatorSvc(CategoryEntity $category) : ResourceItemCreatorSvc
    {
        return $this->service('XFRM:ResourceItem\Create', $category);
    }
}