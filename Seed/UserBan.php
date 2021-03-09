<?php

namespace TickTackk\Seeder\Seed;

use XF\Finder\User as UserFinder;
use XF\Repository\Banning as BanningRepo;
use XF\Entity\User as UserEntity;

class UserBan extends AbstractSeed
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

    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();
        $error = null;

        return $this->getBanningRepo()->banUser(
            \XF::visitor(),
            $faker->boolean ? $faker->dateTimeInInterval('+' . $faker->numberBetween(1, 10) . ' years')->getTimestamp() : 0,
            $faker->boolean ? $faker->text : '',
            $error,
            $this->findRandomModOrAdmin()
        );
    }

    protected function getBanningRepo() : BanningRepo
    {
        return $this->repository('XF:Banning');
    }
}