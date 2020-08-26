<?php

namespace TickTackk\Seeder\Seed;

use XF\Entity\ProfilePost as ProfilePostEntity;
use XF\Service\ProfilePostComment\Creator as ProfilePostCommentCreatorSvc;
use XF\Repository\ThreadWatch as ThreadWatchRepo;
use XF\Service\Thread\Replier as ThreadReplierSvc;

class ProfilePostComment extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        /** @var ProfilePostEntity $randomProfilePost */
        $randomProfilePost = $this->finderWithRandomOrder('XF:ProfilePost')->fetchOne();
        if (!$randomProfilePost)
        {
            return false;
        }

        $faker = $this->faker();

        $profilePostCommentCreatorSvc = $this->getProfileProfileCommentCreator($randomProfilePost);

        if ($faker->boolean)
        {
            $profilePostCommentCreatorSvc->logIp($faker->boolean ? $faker->ipv6 : $faker->ipv4);
        }
        else
        {
            $profilePostCommentCreatorSvc->logIp(false);
        }

        $profilePostCommentCreatorSvc->setContent($faker->text);

        if (!$profilePostCommentCreatorSvc->validate())
        {
            return false;
        }

        $profilePostCommentCreatorSvc->save();

        return true;
    }

    protected function getProfileProfileCommentCreator(ProfilePostEntity $profilePost) : ProfilePostCommentCreatorSvc
    {
        return $this->service('XF:ProfilePostComment\Creator', $profilePost);
    }
}