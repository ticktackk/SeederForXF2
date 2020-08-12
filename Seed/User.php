<?php

namespace TickTackk\Seeder\Seed;

use XF\Phrase;
use XF\Service\User\Registration as UserRegistrationSvc;

/**
 * Class User
 *
 * @package TickTackk\Seeder\Seed
 */
class User extends AbstractSeed
{
    protected function seed(array $params = []): bool
    {
        $faker = $this->faker();

        /** @var UserRegistrationSvc $registrationService */
        $registrationService = $this->service('XF:User\Registration');
        $registrationService->setMapped([
            'username' => $faker->boolean ? $faker->userName : $faker->firstName . '_' . $faker->lastName,
            'email' => $faker->userName . '_' . $faker->email,
            'timezone' => $faker->timezone,
            'location' => $faker->boolean ? $faker->city : ''
        ]);
        $registrationService->setPassword($faker->password, '', false);
        $registrationService->setReceiveAdminEmail($faker->boolean);

        $dob = \explode('-', $faker->dateTimeThisCentury->format('d-m-Y'));
        $registrationService->setDob($dob[0], $dob[1], $dob[2]);
        $registrationService->skipEmailConfirmation();
        $registrationService->setReceiveAdminEmail($faker->boolean);

        if ($faker->boolean)
        {
            $registrationService->setAvatarUrl($faker->imageUrl());
        }

        if (!$registrationService->validate())
        {
            return false;
        }

        if (!$registrationService->save())
        {
            return false;
        }

        return true;
    }
}