<?php

namespace TickTackk\Seeder\Seed;

use XF\Db\DuplicateKeyException;
use XF\Entity\SpamCleanerLog as SpamCleanerLogEntity;
use XF\Entity\User as UserEntity;
use XF\Finder\User as UserFinder;
use XF\Mvc\Entity\Entity;
use XF\Util\Ip as IpUtil;

class SpamCleanerLog extends AbstractSeed
{
    protected function setupVisitorFinder(UserFinder $userFinder): UserFinder
    {
        return $userFinder
            ->where('is_admin', false)
            ->where('is_moderator', false)
            ->where('is_staff', false)
            ->where('is_banned', false);
    }

    protected function findRandomModOrAdmin() : UserEntity
    {
        return $this->finderWithRandomOrder('XF:User')
            ->whereOr(['is_admin', true], ['is_moderator', true])
            ->fetchOne();
    }

    /**
     * @param array $params
     *
     * @return bool
     *
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []): bool
    {
        $randomContent = $this->findRandomContent();
        if (!$randomContent)
        {
            return false;
        }

        $faker = $this->faker();

        /** @var SpamCleanerLogEntity $spamCleanerLog */
        $spamCleanerLog = $this->em()->create('XF:SpamCleanerLog');
        $spamCleanerLog->data = $faker->randomElements();
        $spamCleanerLog->application_date = $faker->dateTime->getTimestamp();
        $spamCleanerLog->restored_date = $faker->boolean ? $faker->dateTime->getTimestamp() : 0;

        $visitor = \XF::visitor();
        $spamCleanerLog->user_id = $visitor->user_id;
        $spamCleanerLog->username = $visitor->username;

        $applyingUser = $this->findRandomModOrAdmin();
        $spamCleanerLog->applying_user_id = $applyingUser->user_id;
        $spamCleanerLog->applying_username = $applyingUser->username;

        try
        {
            return $spamCleanerLog->save();
        }
        catch (DuplicateKeyException $exception)
        {
            return false;
        }
    }

    protected function findRandomContent() :? Entity
    {
        $contentTypes = $this->app()->getContentTypeField('spam_handler_class');
        $contentType = \array_rand($contentTypes);
        $identifier = $this->app()->getContentTypeFieldValue($contentType, 'entity');
        if ($identifier === null)
        {
            return null;
        }

        return $this->finderWithRandomOrder($identifier)->fetchOne();
    }
}