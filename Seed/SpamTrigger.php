<?php

namespace TickTackk\Seeder\Seed;

use XF\Db\DuplicateKeyException;
use XF\Entity\SpamTriggerLog as SpamTriggerLogEntity;
use XF\Finder\User as UserFinder;
use XF\Mvc\Entity\Entity;
use XF\Util\Ip as IpUtil;

class SpamTrigger extends AbstractSeed
{
    protected function setupVisitorFinder(UserFinder $userFinder): UserFinder
    {
        return $userFinder
            ->where('is_admin', false)
            ->where('is_moderator', false)
            ->where('is_staff', false)
            ->where('is_banned', false);
    }

    /**
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

        /** @var SpamTriggerLogEntity $spamTriggerLog */
        $spamTriggerLog = $this->em()->create('XF:SpamTriggerLog');
        $spamTriggerLog->content_type = $randomContent->getEntityContentType();
        $spamTriggerLog->content_id = $randomContent->getEntityId();
        $spamTriggerLog->log_date = $faker->dateTime->getTimestamp();
        $spamTriggerLog->user_id = \XF::visitor()->user_id;
        $spamTriggerLog->result = $faker->text(25);
        $spamTriggerLog->details = $faker->randomElements();
        $spamTriggerLog->request_state = $faker->randomElements();
        $spamTriggerLog->ip_address = IpUtil::convertIpStringToBinary($faker->boolean ? $faker->ipv6 : $faker->ipv4);

        try
        {
            return $spamTriggerLog->save();
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