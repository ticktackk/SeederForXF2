<?php

namespace TickTackk\Seeder\Seed;

use XF\Finder\User as UserFinder;
use XF\Repository\Banning as BanningRepo;
use XF\Entity\User as UserEntity;

class UserReject extends AbstractSeed
{
    protected function setupVisitorFinder(UserFinder $userFinder): UserFinder
    {
        return $userFinder
            ->where('is_admin', false)
            ->where('is_moderator', false)
            ->where('is_staff', false)
            ->where('is_banned', false)
            ->where('user_state', '!=', 'rejected');
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
        $user = \XF::visitor();

        $rejected = $user->rejectUser($faker->boolean ? $faker->text : '', $this->findRandomModOrAdmin());

        if ($rejected && $faker->boolean)
        {
            $dateTimeObj = $faker->dateTimeInInterval('-' . $faker->numberBetween(1, 20) . ' years');
            $user->Reject->fastUpdate('reject_date',  $dateTimeObj->getTimestamp());
        }

        return $rejected;
    }
}