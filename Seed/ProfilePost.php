<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\User as UserEntity;
use XF\Entity\UserProfile as UserProfileEntity;
use XF\Repository\ThreadWatch as ThreadWatchRepo;
use XF\Service\ProfilePost\Creator as ProfilePostCreatorSvc;
use XF\Service\Thread\Replier as ThreadReplierSvc;

class ProfilePost extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        /** @var UserEntity $randomUser */
        $randomUser = $this->finderWithRandomOrder('XF:User')->fetchOne();
        if (!$randomUser)
        {
            return false;
        }

        $faker = $this->faker();

        $profilePostCreatorSvc = $this->getProfilePostCreatorSvc($randomUser->Profile);

        if ($faker->boolean)
        {
            $profilePostCreatorSvc->logIp($faker->boolean ? $faker->ipv4 : $faker->ipv6);
        }
        else
        {
            $profilePostCreatorSvc->logIp(false);
        }

        $profilePostCreatorSvc->setContent($faker->text);
        if (!$profilePostCreatorSvc->validate())
        {
            return false;
        }

        $profilePostCreatorSvc->save();

        return true;
    }

    protected function getProfilePostCreatorSvc(UserProfileEntity $userProfile) : ProfilePostCreatorSvc
    {
        return $this->service('XF:ProfilePost\Creator', $userProfile);
    }
}