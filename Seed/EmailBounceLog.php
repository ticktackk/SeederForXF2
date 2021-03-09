<?php

namespace TickTackk\Seeder\Seed;

use XF\Repository\User as UserRepo;
use XF\Entity\User as UserEntity;
use XF\Entity\EmailBounceLog as EmailBounceLogEntity;
use XF\Util\File as FileUtil;
use XF\Util\Ip as IpUtil;

class EmailBounceLog extends AbstractSeed
{
    /**
     * @var null|string[]
     */
    protected $filesList = null;

    /**
     * @throws \XF\PrintableException
     */
    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();

        /** @var EmailBounceLogEntity $emailBounceLog */
        $emailBounceLog = $this->em()->create('XF:EmailBounceLog');
        $emailBounceLog->user_id = \XF::visitor()->user_id;
        $emailBounceLog->email_date = $faker->dateTime()->getTimestamp();
        $emailBounceLog->log_date = $faker->dateTime()->getTimestamp();
        $emailBounceLog->recipient = $faker->name;
        $emailBounceLog->raw_message = $faker->text;

        if ($faker->boolean)
        {
            $emailBounceLog->action_taken = 'hard';
        }
        else if ($faker->boolean)
        {
            $emailBounceLog->action_taken = 'soft';
        }
        else if ($faker->boolean)
        {
            $emailBounceLog->action_taken = 'soft_hard';
        }
        else if ($faker->boolean)
        {
            $emailBounceLog->action_taken = 'untrusted';
        }
        else if ($faker->boolean)
        {
            $emailBounceLog->action_taken = '';
        }
        else if ($faker->boolean)
        {
            $emailBounceLog->action_taken = $faker->word;
        }

        return $emailBounceLog->save();
    }
}